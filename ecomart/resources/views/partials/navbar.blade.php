<nav class="navbar navbar-expand-lg navbar-dark ecomart-navbar shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="EcoMart logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#categories">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#products">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Store & Contact</a></li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a href="{{ route('shopping') }}" class="btn btn-outline-light btn-sm me-2">
                        <i class="bi bi-basket"></i> List
                    </a>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-success btn-sm ms-lg-2" href="{{ route('signup') }}">
                            Sign Up
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->first_name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    Signed in as<br>
                                    <strong>{{ Auth::user()->email }}</strong>
                                </span>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a class="dropdown-item" href="{{ route('shopping') }}">
                                    <i class="bi bi-basket"></i> My Shopping List
                                </a>
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        <i class="bi bi-box-arrow-right"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>