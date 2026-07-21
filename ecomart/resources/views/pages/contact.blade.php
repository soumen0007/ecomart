@extends('layouts.app')

@section('title', 'Store & Contact · EcoMart')

@section('content')

<section class="container py-5">
    <div class="section-heading text-center mb-5">
        <div class="section-divider"></div>
        <h2>Store & Contact</h2>
        <p>Visit EcoMart or send us a message.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-5">
            <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                <h4>Visit Us</h4>
                <p class="mb-1">128 Garden Place</p>
                <p class="mb-1">Hamilton Central 3204</p>
                <p>Waikato, New Zealand</p>

                <h5 class="mt-4">Opening Hours</h5>
                <p class="mb-1">Mon – Fri: 8am – 7pm</p>
                <p class="mb-1">Saturday: 9am – 6pm</p>
                <p>Sunday: 10am – 4pm</p>

                <h5 class="mt-4">Phone</h5>
                <p>(07) 855 1234</p>
            </div>
        </div>

        <div class="col-md-7">
            <div class="bg-white p-4 rounded-4 shadow-sm">
                <h4>Send Message</h4>

                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text"
                               name="subject"
                               class="form-control"
                               value="{{ old('subject') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message"
                                  class="form-control"
                                  rows="5"
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection