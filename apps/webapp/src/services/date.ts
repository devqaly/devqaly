import toDate from 'date-fns-tz/toDate'
import format from 'date-fns-tz/format'

export const formatToDate = (date: Date | string, formatString = 'dd/MM/yyyy') => {
  let dateObject: Date

  if (typeof date === 'string') {
    dateObject = toDate(date)
  } else {
    dateObject = date
  }

  return format(dateObject, formatString)
}

export const formatToDateTime = (date: Date | string) => {
  let dateObject: Date

  if (typeof date === 'string') {
    dateObject = toDate(date)
  } else {
    dateObject = date
  }

  return format(dateObject, 'dd/MM/yyyy HH:mm')
}
