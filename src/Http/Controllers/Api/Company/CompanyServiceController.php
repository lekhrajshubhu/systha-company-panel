<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\QuestionTransition;
use Systha\Core\Models\CompanyServiceItem;

class CompanyServiceController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $company = $request->get('company'); // Attached by CompanyContextFromToken middleware
        
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $query = CompanyServiceItem::query()
            ->where('company_id', $company->id)
            ->with([
                'serviceItem.serviceGroup.category',
                'serviceItem.serviceType',
            ])
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->whereHas('serviceItem', function ($q) use ($search): void {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (CompanyServiceItem $mapping): array {
            return [
                'id' => $mapping->id,
                'service_name' => $mapping->serviceItem?->name,
                'category_name' => $mapping->serviceItem?->serviceGroup?->category?->name,
                'group_name' => $mapping->serviceItem?->serviceGroup?->name,
                'is_active' => (bool) $mapping->is_active,
                'base_price' => $mapping->base_price,
                'service_type' => $mapping->serviceItem?->serviceType?->name,
                'meta' => [
                    'color' => $mapping->serviceItem?->meta['color'] ?? null,
                    'icon_md' => $mapping->serviceItem?->meta['icon_md'] ?? null,
                ],
                'currency' => $mapping->currency,
                'lead_time_hours' => $mapping->lead_time_hours,
                'updated_at' => optional($mapping->updated_at)->toDateTimeString(),
            ];
        });

        return $this->paginatedResponse($paginator);
    }

    public function show(Request $request, $companyId, $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $mapping = CompanyServiceItem::query()
            ->where('company_id', $company->id)
            ->where('id', $id)
            ->with([
                'serviceItem.serviceGroup.category',
                'serviceItem.serviceGroup.questions.options',
                'serviceItem.serviceGroup.serviceItems',
                'serviceItem.questions.options',
                'serviceItem.directQuestions.options',
            ])
            ->first();

        if (!$mapping) {
            return response()->json(['message' => 'Service not found.'], 404);
        }

        $serviceItem = $mapping->serviceItem;
        $clientFlow = [];
        $groupFlow = [];
        $itemFlow = [];

        if ($serviceItem) {
            $transitions = collect();
            if (Schema::hasTable('svc_question_transitions')) {
                $transitions = QuestionTransition::query()
                    ->where('service_item_id', $serviceItem->id)
                    ->get();
            }

            $transitionByFrom = $transitions->groupBy('from_question_id');
            $transitionByOption = $transitions->keyBy('question_option_id');

            $groupQuestions = $serviceItem->serviceGroup?->questions ?? collect();
            foreach ($groupQuestions as $question) {
                $clientFlow[] = $this->normalizeQuestionForFlow(
                    $question,
                    'service_group',
                    (int) ($question->pivot?->sort_order ?? 0),
                    $transitionByFrom,
                    $transitionByOption,
                    $serviceItem
                );
            }

            $itemQuestions = $serviceItem->questions;
            if ($itemQuestions->isEmpty()) {
                $itemQuestions = $serviceItem->directQuestions;
            }

            foreach ($itemQuestions as $index => $question) {
                $sortOrder = $question->pivot?->sort_order ?? ($index + 1);
                $clientFlow[] = $this->normalizeQuestionForFlow(
                    $question,
                    'service_item',
                    (int) $sortOrder,
                    $transitionByFrom,
                    $transitionByOption,
                    $serviceItem
                );
            }

            $groupFlow = array_values(array_filter($clientFlow, function (array $item): bool {
                return $item['source'] === 'service_group';
            }));

            $itemFlow = array_values(array_filter($clientFlow, function (array $item): bool {
                return $item['source'] === 'service_item';
            }));

            $sortByOrder = function (array $a, array $b): int {
                if ($a['sort_order'] === $b['sort_order']) {
                    return $a['id'] <=> $b['id'];
                }

                return $a['sort_order'] <=> $b['sort_order'];
            };

            usort($groupFlow, $sortByOrder);
            usort($itemFlow, $sortByOrder);

            $clientFlow = array_merge($groupFlow, $itemFlow);
        }

        return response()->json([
            'id' => $mapping->id,
            'service_item_id' => $mapping->service_item_id,
            'name' => $mapping->serviceItem?->name,
            'category_name' => $mapping->serviceItem?->serviceGroup?->category?->name,
            'group_name' => $mapping->serviceItem?->serviceGroup?->name,
            'is_active' => (bool) $mapping->is_active,
            'base_price' => $mapping->base_price,
            'meta' => [
                'color' => $mapping->serviceItem?->meta['color'] ?? null,
                'icon_md' => $mapping->serviceItem?->meta['icon_md'] ?? null,
            ],
            'currency' => $mapping->currency,
            'questions' => $mapping->serviceItem?->questions,
            'direct_questions' => $mapping->serviceItem?->directQuestions,
            'client_flow' => $clientFlow,
            'group_level_questions' => $groupFlow,
            'service_item_level_questions' => $itemFlow,
            'combined_questions' => $clientFlow,
            'lead_time_hours' => $mapping->lead_time_hours,
            'updated_at' => optional($mapping->updated_at)->toDateTimeString(),
        ]);
    }

    private function normalizeQuestionForFlow(
        $question,
        string $source,
        int $sortOrder,
        $transitionByFrom,
        $transitionByOption,
        $serviceItem = null
    ): array {
        $options = $question->options ?? collect();

        if ($options->isEmpty() && $source === 'service_group' && $question->code === 'service_items') {
            $serviceItems = $serviceItem?->serviceGroup?->serviceItems ?? collect();
            $options = $serviceItems->map(function ($item, int $index) {
                return (object) [
                    'id' => $item->id,
                    'label' => $item->name,
                    'value' => (string) $item->id,
                    'price_adjustment' => 0,
                    'sort_order' => $index + 1,
                    'next_question_id' => null,
                ];
            });
        }

        $normalizedOptions = $options->map(function ($option) use ($transitionByOption): array {
            $transition = $transitionByOption->get($option->id);

            return [
                'id' => $option->id,
                'label' => $option->label,
                'value' => $option->value,
                'price_adjustment' => $option->price_adjustment,
                'sort_order' => $option->sort_order,
                'next_question_id' => $option->next_question_id,
                'effective_next_question_id' => $transition?->to_question_id ?? $option->next_question_id,
            ];
        })->values()->all();

        $transitions = $transitionByFrom->get($question->id, collect())
            ->map(function ($transition): array {
                return [
                    'id' => $transition->id,
                    'question_option_id' => $transition->question_option_id,
                    'to_question_id' => $transition->to_question_id,
                    'action_type' => $transition->action_type,
                    'priority' => $transition->priority,
                ];
            })->values()->all();

        return [
            'id' => $question->id,
            'code' => $question->code,
            'title' => $question->title,
            'field_type' => $question->field_type,
            'is_required' => (bool) $question->is_required,
            'is_start' => (bool) ($question->pivot?->is_start ?? $question->is_start),
            'is_active' => (bool) $question->is_active,
            'source' => $source,
            'sort_order' => $sortOrder,
            'previous_question_id' => $question->previous_question_id,
            'next_question_id' => $question->next_question_id,
            'options' => $normalizedOptions,
            'transitions' => $transitions,
        ];
    }
}
