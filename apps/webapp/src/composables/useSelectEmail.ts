import { ref } from 'vue'
import { string } from 'yup'
import { useField, useForm } from 'vee-validate'

export function useSelectEmail() {
  const emails = ref<string[]>([])

  const onRemoveEmail = (email: string) => {
    emails.value = emails.value.filter((e) => e !== email)
  }

  const { handleSubmit, resetForm } = useForm()

  const { value: email, errorMessage } = useField<string>(
    'email',
    string().required().email('Must be a valid email')
  )

  const onSubmit = handleSubmit((values) => {
    resetForm()

    if (emails.value.includes(values.email)) return

    emails.value.unshift(values.email)
  })

  return {
    emails,
    onRemoveEmail,
    handleSubmit,
    resetForm,
    email,
    errorMessage,
    onSubmit
  }
}
