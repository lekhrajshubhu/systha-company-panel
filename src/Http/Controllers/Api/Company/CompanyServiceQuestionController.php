<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\ServiceGroup;
use Systha\Core\Models\CompanyServiceItem;

class CompanyServiceQuestionController extends Controller
{
    use HandlesApiResources;

    public function serviceItemQuestions(Request $request, $companyId, $id)
    {
        $company = $request->get('company'); // Attached by CompanyContextFromToken middleware
        
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $mapping = CompanyServiceItem::query()
            ->where('company_id', $company->id)
            ->where('id', $id)
            ->with([
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
            $transitionByFrom = collect();
            $transitionByOption = collect();

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

        $type = $request->query('type', 'combined');

        if ($type === 'item_level') {
            return response()->json($itemFlow);
        }

        if ($type === 'group_level') {
            return response()->json($groupFlow);
        }

        return response()->json($clientFlow);
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

    public function serviceGroupItemQuestions(Request $request, $companyId, $id)
    {
        $company = $request->get('company'); // Attached by CompanyContextFromToken middleware

        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $serviceGroup = ServiceGroup::query()
            ->where('id', $id)
            ->with([
                'serviceTypes',
                'questions.options',
            ])
            ->first();

        if (!$serviceGroup) {
            return response()->json(['message' => 'Service group not found.'], 404);
        }

        $companyItems = CompanyServiceItem::query()
            ->where('company_id', $company->id)
            ->whereHas('serviceItem', function ($query) use ($serviceGroup): void {
                $query->where('service_group_id', $serviceGroup->id);
            })
            ->with('serviceItem')
            ->get()
            ->pluck('serviceItem')
            ->filter();

        $typeOptions = $serviceGroup->serviceTypes
            ->sortBy('id')
            ->values()
            ->map(function ($type, int $index): array {
                return [
                    'id' => $type->id,
                    'label' => $type->name,
                    'value' => (string) $type->id,
                    'price_adjustment' => 0,
                    'sort_order' => $index + 1,
                    'next_question_id' => null,
                    'effective_next_question_id' => null,
                ];
            })->all();

        $itemOptions = $companyItems
            ->sortBy('id')
            ->values()
            ->map(function ($item, int $index): array {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                    'value' => (string) $item->id,
                    'service_type_id' => $item->service_type_id,
                    'price_adjustment' => 0,
                    'sort_order' => $index + 1,
                    'next_question_id' => null,
                    'effective_next_question_id' => null,
                ];
            })->all();

        $flow = [
            [
                'id' => -1,
                'code' => 'service_types',
                'title' => 'Service Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_start' => true,
                'is_active' => true,
                'source' => 'service_group',
                'sort_order' => 1,
                'previous_question_id' => null,
                'next_question_id' => null,
                'options' => $typeOptions,
                'transitions' => [],
            ],
            [
                'id' => -2,
                'code' => 'service_items',
                'title' => 'Service Item',
                'field_type' => 'select',
                'is_required' => true,
                'is_start' => false,
                'is_active' => true,
                'source' => 'service_group',
                'sort_order' => 2,
                'previous_question_id' => null,
                'next_question_id' => null,
                'options' => $itemOptions,
                'transitions' => [],
            ],
        ];

        $transitionByFrom = collect();
        $transitionByOption = collect();

        $groupQuestions = $serviceGroup->questions
            ->filter(function ($question): bool {
                return !in_array($question->code, ['service_items', 'service_types'], true);
            })
            ->values();

        foreach ($groupQuestions as $question) {
            $sortOrder = (int) ($question->pivot?->sort_order ?? 0);
            $normalized = $this->normalizeQuestionForFlow(
                $question,
                'service_group',
                $sortOrder + 2,
                $transitionByFrom,
                $transitionByOption,
                null
            );
            $flow[] = $normalized;
        }

        usort($flow, function (array $a, array $b): int {
            if ($a['sort_order'] === $b['sort_order']) {
                return $a['id'] <=> $b['id'];
            }

            return $a['sort_order'] <=> $b['sort_order'];
        });

        return response()->json($flow);
    }
}
