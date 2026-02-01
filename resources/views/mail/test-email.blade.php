Hay {{ $name }}
This is a test email for email verification.
Please click the link below to verify your email address:
{{-- <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) }}">Verify Email</a> --}}
