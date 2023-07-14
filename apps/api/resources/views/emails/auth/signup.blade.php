<x-mail::message>
# Welcome to {{config('app.name')}}

You can click bellow to finish your registration

<x-mail::button :url="$finishRegistrationUrl">
Finish Registration
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
