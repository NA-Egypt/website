<x-frontend.layout>
    <x-section-head>{{ __('messages.contactus') }}</x-section-head>
        <div class="card" @if(app()->getLocale() === 'ar') style="direction:rtl;" @endif>
            <div class="card-body">
                <div class="border p-3 rounded">
                    <form class="row g-3" id="form" method="POST" action="{{ route('contactus.store') }}">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('messages.Please enter your name') }}" required class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('messages.Please enter your email') }}" required class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Message') }}</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" rows="4" cols="4" name="message" placeholder="{{ __('messages.Please enter your message') }}" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @error('g-recaptcha-response')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                            <button type="submit" class="btn btn-primary g-recaptcha" 
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" 
                            data-callback="onSubmit" 
                            data-action="submit">{{ __('messages.Send') }}</button>
                            </div>
                        </div>
                    </form>
                     <script>
                        function onSubmit(token) {
                            document.getElementById("form").submit();
                        }
                    </script>
                    @if(session('status') === 'mail-sent')
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                Swal.fire({
                                    title: '{{__("messages.Success!")}}',
                                    text: '{{__("messages.mail-sent")}}',
                                    icon: 'success',
                                    confirmButtonText: '{{__("messages.Done")}}'
                                });
                            });
                        </script>
                    @endif
                </div>
            </div>
        </div>
</x-frontend.layout>