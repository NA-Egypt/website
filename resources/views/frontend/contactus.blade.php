<x-frontend.layout>
    <x-section-head>{{ __('messages.contactus') }}</x-section-head>
        <div class="card" @if(app()->getLocale() === 'ar') style="direction:rtl;" @endif>
            <div class="card-body">
                <div class="border p-3 rounded">
                    <form class="row g-3" method="POST" action="{{ route('contactus.store') }}">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Name') }}</label>
                            <input type="text" name="name" placeholder="{{ __('messages.Please enter your name') }}" required class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Email') }}</label>
                            <input type="email" name="email" placeholder="{{ __('messages.Please enter your email') }}" required class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Message') }}</label>
                            <textarea class="form-control" rows="4" cols="4" name="message" placeholder="{{ __('messages.Please enter your message') }}" required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('messages.Send') }}</button>
                            </div>
                        </div>
                    </form>
                    @if(session('status') === 'mail-sent')
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                Swal.fire({
                                    title: '{{__("messages.Success!")}}',
                                    text: '{{__("messages.Mail has been sent successfully.")}}',
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

