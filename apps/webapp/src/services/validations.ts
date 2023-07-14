import type { ObjectSchema, InferType } from 'yup'
import type { FormActions } from 'vee-validate'

export function getSubmitFn<Schema extends ObjectSchema<Record<string, any>>>(
  _: Schema,
  callback: (values: InferType<Schema>, actions: FormActions<InferType<Schema>>) => void
) {
  return (values: Record<string, any>, actions: FormActions<InferType<Schema>>) => {
    return callback(values, actions)
  }
}
