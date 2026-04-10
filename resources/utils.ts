export function formatCurrency(value: string | number | undefined | null) {
  if (value == null || value === '') return '-'
  const num = typeof value === 'number' ? value : parseFloat(String(value))
  if (Number.isNaN(num)) return String(value)
  return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }).format(num)
}

export function formatDate(value: string | undefined | null) {
  if (!value) return '-'
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return String(value)

  return new Intl.DateTimeFormat(undefined, {
    month: 'numeric',
    day: 'numeric',
    year: 'numeric',
  }).format(d)
}

export const formatDurationMinutes = (minutes?: number | null): string => {
  if (minutes === null || minutes === undefined) {
    return ""
  }

  if (minutes < 60) {
    return `${minutes}m`
  }

  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60

  if (remainingMinutes === 0) {
    return `${hours}h`
  }

  return `${hours}h ${remainingMinutes}m`
}

// Map status to semantic color token names expected by the UI
// Returns one of: 'success', 'info', 'warning', 'error'
export function getStatusColor(status?: string | null): string {
  if (!status) return 'info'
  switch (String(status).toLowerCase()) {
    case 'active':
      return 'success'
    case 'inactive':
      return 'info'
    case 'pending':
      return 'warning'
    case 'suspended':
      return 'error'
    default:
      return 'info'
  }
}

export function getRoleColor(role?: string | null): string {
  if (!role) return 'default'
  switch (String(role).toLowerCase()) {
    case 'admin':
      return 'primary'
    case 'manager':
      return 'secondary'
    case 'supervisor':
      return 'info'
    case 'employee':
      return 'success'
    case 'customer':
      return 'warning'
    case 'staff':
      return 'default'
    default:
      return 'default'
  }
}
