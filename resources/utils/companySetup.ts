const STORAGE_PREFIX = 'companypanel_setup'

type SetupStep = 'password' | 'payment' | 'plans'

const safeJsonParse = (raw: string | null): any => {
  if (!raw) return null
  try {
    return JSON.parse(raw)
  } catch {
    return null
  }
}

const getCompanyCodeFromAccount = (): string => {
  const account = safeJsonParse(localStorage.getItem('companypanel_account'))
  const code = account?.company?.code
  return typeof code === 'string' ? code : ''
}

export const getCompanySetupKey = (companyCode?: string): string => {
  const code = companyCode ?? getCompanyCodeFromAccount()
  return code ? `${STORAGE_PREFIX}:${code}` : `${STORAGE_PREFIX}:__global__`
}

export const getCompanySetupState = (
  companyCode?: string,
): { completed: boolean; steps: Record<SetupStep, boolean> } => {
  const raw = safeJsonParse(localStorage.getItem(getCompanySetupKey(companyCode)))

  const steps: Record<SetupStep, boolean> = {
    password: Boolean(raw?.steps?.password),
    payment: Boolean(raw?.steps?.payment),
    plans: Boolean(raw?.steps?.plans),
  }

  const completed =
    Boolean(raw?.completed) ||
    (steps.password && steps.payment && steps.plans)

  return { completed, steps }
}

export const setCompanySetupStep = (step: SetupStep, value: boolean, companyCode?: string) => {
  const key = getCompanySetupKey(companyCode)
  const current = safeJsonParse(localStorage.getItem(key)) ?? {}
  const nextSteps = { ...(current.steps ?? {}), [step]: Boolean(value) }
  const completed = Object.values(nextSteps).every(Boolean)
  localStorage.setItem(key, JSON.stringify({ ...current, steps: nextSteps, completed }))
}

export const setCompanySetupCompleted = (value: boolean, companyCode?: string) => {
  const key = getCompanySetupKey(companyCode)
  const current = safeJsonParse(localStorage.getItem(key)) ?? {}
  localStorage.setItem(key, JSON.stringify({ ...current, completed: Boolean(value) }))
}

export const getPostLoginRouteName = (): 'company.setup' | 'company.dashboard' => {
  const { completed } = getCompanySetupState()
  return completed ? 'company.dashboard' : 'company.setup'
}
