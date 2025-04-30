<!DOCTYPE html>
<html lang="en">

<head>
    <title>AuthCentral - {{ $title }} </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="shuffle-for-bootstrap.png">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Custom scrollbar styling - vertical only */
        ::-webkit-scrollbar {
            width: 6px;
            height: 0; /* Remove horizontal scrollbar */
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Form container styles for vertical scrolling only */
        .auth-form-container {
            max-height: 100%;
            overflow-y: auto;
            overflow-x: hidden; /* Hide horizontal scrollbar */
            padding: 1rem 0;
            width: 100%;
        }
        
        /* Make sure content doesn't overflow horizontally */
        form, .form-group, .row {
            max-width: 100%;
            word-wrap: break-word;
        }
        
        /* Responsive adjustments */
        @media (max-height: 800px) {
            .auth-logo {
                height: 100px !important;
                margin-bottom: 0.5rem;
            }
            
            .mb-7 {
                margin-bottom: 1rem !important;
            }
        }
        
        /* Make the auth section responsive */
        .auth-section {
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden; /* Prevent horizontal scrolling at the section level */
            display: flex;
            flex-direction: column;
        }
        
        /* Ensure full height for both columns */
        .auth-row {
            flex: 1;
            min-height: 100vh;
            margin: 0;
            width: 100%;
        }
        
        .testimonial-side {
            background-color: #f8f9fa;
            min-height: 100vh;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        /* Ensure form side takes full height */
        .form-side {
            min-height: 100vh;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <section data-from-ai="true" class="auth-section bg-white position-relative"
        style="background-image: url('{{ asset('images/pattern-light.png') }}')">
        <div class="top-0 position-absolute start-0 h-100 w-100"
            style="background: radial-gradient(50% 50% at 50% 50%, rgba(255, 255, 255, 0) 0%, #FFFFFF 100%);"></div>
        <div class="position-relative row auth-row g-0" style="z-index:1;">
            <div class="col-12 col-md-6 form-side">
                <div class="px-4 mx-auto mb-7 text-center mw-md">
                    <img class="img-fluid auth-logo" style="height: 150px;" src="{{ asset('images/pnmtc-logo.png') }}"
                        alt="">
                    <h2 class="mb-4 font-heading fs-7">{{ $description }}</h2>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="auth-form-container">
                    {{ $slot }}
                </div>
            </div>
            <div class="col-12 col-md-6 testimonial-side">
                <div class="mx-auto text-center mw-md-xl quotes">
                    <span class="mb-4 shadow badge bg-primary-dark text-primary text-uppercase">TESTIMONIALS</span>
                    <div class="mb-20 position-relative">
                        <h2 class="position-relative font-heading fs-7 fw-medium text-light-dark"
                            style="z-index: 1;">Love the simplicity of the service and the prompt customer
                            support. We can't imagine working without it.</h2>
                        <img class="top-0 position-absolute start-0 ms-n12 mt-n10"
                            src="flex-assets/images/sign-in/quote-top.svg" alt="">
                        <img class="bottom-0 position-absolute end-0 me-n10 mb-n16"
                            src="flex-assets/images/sign-in/quote-down.svg" alt="">
                    </div>
                    <img class="mb-6 img-fluid" src="flex-assets/images/sign-in/avatar.png" alt="">
                    <h3 class="mb-1 font-heading fs-10 fw-semibold text-light-dark">John Doe</h3>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Sample quotes data array
        const quoteData = [{
                text: "I have worked with Manifest Digital for 2+ years and I like how they work with speed. Their works are neat with beautiful interfaces. Thanks for making Yve Digital & Get The Artiste platforms what they are.",
                author: "Kwame Baah, CEO & Founder at Yve Digital (GH)"
            },
            {
                text: "Having worked with manifest Ghana, I will recommend them to any client. They are professional, time-efficient and productivity is of a high standard.",
                author: "Esther Yeboah, CEO at Eunson Consulting (UK)"
            },
            {
                text: "I have given them five stars for the excellent work done. I recommend them to anyone or organization that needs a fast, efficient, and reliable website for any purpose.",
                author: "Mawuli Nyador (GH) "
            }
        ];

        let index = 0;
        const slideTime = 5000; // 5 seconds interval for auto-slide
        let autoSlideInterval;

        const quotes = document.querySelector('.quotes');
        const quoteText = quotes.querySelector('h2');
        const quoteAuthor = quotes.querySelector('h3');
        const dotsContainer = document.createElement('div');

        dotsContainer.classList.add('row', 'justify-content-center', 'g-3', 'mt-4');

        // Create dots dynamically
        quoteData.forEach((_, i) => {
            const dot = document.createElement('div');
            dot.classList.add('col-auto');
            dot.innerHTML =
                `<a class="d-inline-block rounded-pill bg-light" style="width: 12px; height: 12px; cursor: pointer;" href="#"></a>`;
            dot.querySelector('a').addEventListener('click', () => {
                navigateToQuote(i);
            });
            dotsContainer.appendChild(dot);
        });

        quotes.appendChild(dotsContainer);
        const dots = dotsContainer.querySelectorAll('a');

        // Function to update quote text and author
        function updateQuote() {
            quoteText.innerText = quoteData[index].text;
            quoteAuthor.innerText = quoteData[index].author;

            // Update dots (previous, current, and next)
            dots.forEach((dot, i) => {
                dot.classList.remove('bg-primary', 'bg-light');
                if (i === index) {
                    dot.classList.add('bg-primary'); // Current dot is green
                } else {
                    dot.classList.add('bg-light'); // Other dots are light
                }
            });
        }

        // Auto slide function
        function autoSlide() {
            index = (index + 1) % quoteData.length;
            updateQuote();
        }

        // Navigate to a specific quote
        function navigateToQuote(i) {
            index = i;
            updateQuote();
            clearInterval(autoSlideInterval); // Stop auto-slide on manual navigation
            autoSlideInterval = setInterval(autoSlide, slideTime); // Restart auto-slide
        }

        // Initialize first quote and auto-slide
        updateQuote();
        autoSlideInterval = setInterval(autoSlide, slideTime);
    </script>

    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
