<footer class="ecomart-footer">

    <div class="container py-4">

        <div class="row gy-4">

            <div class="col-md-4">

                <img src="{{ asset('assets/images/logo.jpg') }}"
                     alt="EcoMart Logo"
                     style="background:rgba(255,255,255,0.9);
                            padding:0.5rem;
                            border-radius:0.5rem;
                            height:40px;">

                <p class="small text-light-emphasis mt-2">

                    Hamilton's home for fresh organic groceries and eco-friendly
                    living. Browse online and enjoy a seamless in-store shopping
                    experience with locally sourced products.

                </p>

            </div>

            <div class="col-6 col-md-2">

                <h6 class="footer-heading">

                    Browse

                </h6>

                <ul class="list-unstyled footer-links">

                    <li>

                        <a href="{{ route('home') }}">

                            Home

                        </a>

                    </li>

                    <li>

                        <a href="{{ route('home') }}#products">

                            Products

                        </a>

                    </li>

                    <li>

                        <a href="{{ route('contact') }}">

                            Store Info

                        </a>

                    </li>

                </ul>

            </div>

            <div class="col-6 col-md-3">

                <h6 class="footer-heading">

                    Visit Us

                </h6>

                <address class="small mb-0">

                    128 Garden Place <br>

                    Hamilton Central 3204 <br>

                    Waikato, New Zealand <br>

                    <abbr title="Phone">P:</abbr>

                    (07) 855 1234

                </address>

            </div>

            <div class="col-md-3">

                <h6 class="footer-heading">

                    Opening Hours

                </h6>

                <ul class="list-unstyled small">

                    <li>Monday – Friday : 8:00 AM – 7:00 PM</li>

                    <li>Saturday : 9:00 AM – 6:00 PM</li>

                    <li>Sunday : 10:00 AM – 4:00 PM</li>

                </ul>

            </div>

        </div>

        <hr class="my-3 border-light-subtle">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

            <p class="small mb-2 mb-md-0">

                &copy; {{ date('Y') }} EcoMart. All rights reserved.

            </p>

            <p class="small mb-0">

                <a href="{{ route('admin.login') }}"
                   class="footer-link-admin">

                    Staff Login

                </a>

            </p>

        </div>

    </div>

</footer>