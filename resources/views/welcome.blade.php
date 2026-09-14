<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Peminjaman alat | Beranda</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('enno/assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('enno/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('enno/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/css/main.css') }}" rel="stylesheet">

  <style>
    /* Animasi naik–turun */
    .btn-float {
      animation: float-btn 3s ease-in-out infinite;
      transition: background-color 0.2s ease, transform 0.1s ease;
      border-radius: 50px;
      /* PENTING: jaga bentuk oval */
    }

    /* Gerakan naik–turun */
    @keyframes float-btn {
      0% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-6px);
      }

      100% {
        transform: translateY(0);
      }
    }

    /* Saat ditekan */
    .btn-float:active,
    .btn-float:focus {
      background-color: #0b5ed7;
      color: #ffffff;
      transform: scale(0.95);
      border-radius: 50px;
      /* jaga oval saat klik */
    }

    /* Stop animasi saat ditekan */
    .btn-float:active {
      animation: none;
    }
  </style>

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="#" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">Peminjaman Alat</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Beranda</a></li>
          <li><a href="#about">Tentang kita</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <!-- Navbar login (TANPA animasi) -->
      <a class="btn-getstarted" href="{{ route('login') }}">Login</a>
    </div>
  </header>

  <main class="main">

    <section id="hero" class="hero section">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
            <h1>Aplikasi Peminjaman Alat Modern</h1>
            <p>Sistem peminjaman alat yang dirancang untuk memudahkan proses peminjaman secara cepat, aman, dan
              terorganisir.</p>
            <div class="d-flex">
              <a href="{{ route('login') }}" class="btn-get-started btn-float">
                Login
              </a>


            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="100">
            <img src="{{ asset('enno/assets/img/hero-img.png') }}" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>

    </section>

    <section id="about" class="about section">

      <div class="container section-title" data-aos="fade-up">
        <span>tentang kita<br></span>
        <h2>tentang kita</h2>

        <p>Solusi Peminjaman Alat yang Mudah dan Terpercaya</p>
      </div>

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">

          </div>
          <div class="col-xl-12 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Sistem Peminjaman Alat yang Efektif dan Terstruktur</h3>
            <p class="fst-italic">
              Aplikasi Peminjaman Alat merupakan sistem berbasis web yang bertujuan untuk mempermudah pengguna dalam
              melakukan peminjaman dan pengelolaan alat secara terstruktur.
            </p>
            <ul>
              <li><i class="bi bi-check2-all"></i> <span>Proses peminjaman yang mudah dan cepat</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Data peminjaman tersimpan dengan rapi</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Mengurangi kesalahan dalam pencatatan manual</span></li>
            </ul>
            <p>
              Dengan adanya aplikasi ini, diharapkan proses peminjaman alat dapat berjalan lebih tertib dan transparan.
            </p>
          </div>
        </div>

      </div>

    </section>

    <section id="contact" class="contact section">

      <div class="container section-title" data-aos="fade-up">
        <span>Hubungi Kami</span>
        <h2>Kontak</h2>
        <p>Jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut, silakan hubungi kami melalui kontak di
          bawah ini.</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-xl-12">

            <div class="info-wrap">
              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h3>Alamat</h3>
                  <p>Jl. Talaga Bantarujeg, Mekarraharja, Kec. Talaga, Kabupaten Majalengka, Jawa Barat 45463</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-telephone flex-shrink-0"></i>
                <div>
                  <h3>Hubungi Kami</h3>
                  <p>+62 123-4567-8910</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h3>Email Kami</h3>
                  <p>Tugas@gmail.com</p>
                </div>
              </div><!-- End Info Item -->

              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3702.1819792894216!2d108.29176366983799!3d-6.977773416679415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f3bd1661739f7%3A0x494597a8fa250a4d!2sSMK%20Negeri%201%20Talaga%20Campus%202!5e0!3m2!1sid!2sid!4v1770010915543!5m2!1sid!2sid"
                frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer">

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Peminjaman Alat</strong> <span>All Rights
          Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
          href=“https://themewagon.com>ThemeWagon
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('enno/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

</body>

</html>