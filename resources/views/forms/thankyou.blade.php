<x-frontend.layout>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="card shadow-lg border-0 rounded-4 p-5" style="background: white;">
                    <div class="mb-4 text-success">
                        <i class="bi bi-check-circle-fill" style="font-size: 5rem; display: inline-block;"></i>
                    </div>
                    <h2 class="fw-bold mb-3" style="color: #1a202c;">Thank You!</h2>
                    <p class="text-secondary mb-4">Your response for <strong>{{ $form->title }}</strong> has been successfully submitted.</p>
                    <div class="d-grid gap-2 col-8 mx-auto">
                        <a href="{{ route('frontend.home') }}" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm" style="background: #3b82f6; border: none;">
                            Back to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-frontend.layout>
