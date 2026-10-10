<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIPUS-Del' }} — Perpustakaan Institut Teknologi Del</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Instrument Serif -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --del-purple: #4C1D95;
            --del-purple-hover: #3B0764;
            --del-purple-light: #F5F3FF;
            --del-purple-border: #DDD6FE;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.02em;
        }

        /* Editorial Serif Font Styles */
        .font-serif {
            font-family: 'Instrument Serif', Georgia, serif;
        }

        .font-editorial {
            font-family: 'Instrument Serif', Georgia, serif;
            font-style: italic;
        }

        h1 em, h2 em, h3 em,
        .serif-accent {
            font-family: 'Instrument Serif', Georgia, serif;
            font-style: italic;
            font-weight: 400;
        }

        .navbar-del {
            background-color: var(--del-purple);
            border-bottom: 2px solid #5B21B6;
        }

        .navbar-del .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #FFFFFF !important;
        }

        .navbar-del .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .navbar-del .nav-link:hover,
        .navbar-del .nav-link.active {
            color: #FFFFFF !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .btn-del {
            background-color: var(--del-purple);
            color: #FFFFFF;
            border: 1px solid var(--del-purple);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-del:hover {
            background-color: var(--del-purple-hover);
            color: #FFFFFF;
            border-color: var(--del-purple-hover);
        }

        .btn-outline-del {
            color: var(--del-purple);
            border-color: var(--del-purple);
            font-weight: 500;
        }

        .btn-outline-del:hover {
            background-color: var(--del-purple);
            color: #FFFFFF;
        }

        .badge-del {
            background-color: var(--del-purple-light);
            color: var(--del-purple);
            border: 1px solid var(--del-purple-border);
            font-weight: 600;
        }

        .card-custom {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            background: #FFFFFF;
        }

        .table-custom thead th {
            background-color: #F1F5F9;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #E2E8F0;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .table-custom tbody td {
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .pagination {
            margin-bottom: 0;
            gap: 3px;
        }

        .page-item .page-link {
            color: var(--del-purple);
            border-radius: 6px !important;
            border: 1px solid #E2E8F0;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
            background-color: #FFFFFF;
        }

        .page-item .page-link:hover {
            background-color: var(--del-purple-light);
            border-color: var(--del-purple-border);
            color: var(--del-purple-hover);
        }

        .page-item.active .page-link {
            background-color: var(--del-purple);
            border-color: var(--del-purple);
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(76, 29, 149, 0.25);
        }

        .page-item.disabled .page-link {
            color: #94A3B8;
            background-color: #F8FAFC;
            border-color: #E2E8F0;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-del shadow-sm">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('buku.index') }}">
                <span class="badge bg-white rounded-pill p-2" style="color: #4C1D95 !important;">
                    <i class="bi bi-book-half fs-6"></i>
                </span>
                <span>SIPUS-Del</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}" href="{{ route('buku.index') }}">
                            <i class="bi bi-book-half me-1"></i> Katalog Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags-fill me-1"></i> Kategori Buku
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container-xl py-4">
        <!-- Flash Session Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-shield-fill-x me-2 fs-5"></i>
                    <strong>Terdapat galat validasi formulir:</strong>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Slot Content -->
        {{ $slot }}
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>