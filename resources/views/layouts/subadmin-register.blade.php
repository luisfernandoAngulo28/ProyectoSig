{{ header('X-UA-Compatible: IE=edge,chrome=1') }}
<!doctype html>
<html class="loading" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro de Conductor | AnDre</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Regístrate como conductor en AnDre y comienza a ganar hoy." />
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/favicon-57.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/favicon-72.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/favicon-114.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/master.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        * { box-sizing: border-box; }
        html, body { overflow: auto !important; height: 100%; font-family: 'Inter', 'Montserrat', sans-serif; }

        /* ── LEFT PANEL ── */
        .left__section {
            background: linear-gradient(160deg, #0d1b2a 0%, #1a3a4a 50%, #0e6e5e 100%) !important;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 40px 30px; min-height: 100vh; position: sticky; top: 0;
        }
        .left__section .content__logo-information {
            display: flex; flex-direction: column; align-items: center; gap: 28px; width: 100%;
        }
        .left__section img { max-width: 150px; display: block; }

        /* Tarjeta blanca para el logo */
        .logo-card {
            display: inline-block;
            background: white;
            padding: 16px 28px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .logo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.35);
        }
        .left-welcome { text-align: center; color: white; }
        .left-welcome h2 { font-size: 19px; font-weight: 700; margin-bottom: 8px; color: #5eead4; }
        .left-welcome p { font-size: 13px; color: rgba(255,255,255,0.72); line-height: 1.6; margin: 0; }

        .left-steps { width: 100%; display: flex; flex-direction: column; gap: 12px; }
        .left-step {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px;
            border-radius: 12px; background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .left-step.active { background: rgba(94,234,212,0.15); border-color: rgba(94,234,212,0.35); }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step-num svg { width: 16px; height: 16px; stroke: rgba(255,255,255,0.6); }
        .left-step.active .step-num { background: #0d9488; }
        .left-step.active .step-num svg { stroke: white; }
        .step-label { font-size: 13px; color: rgba(255,255,255,0.75); font-weight: 500; }
        .left-step.active .step-label { color: #5eead4; }

        .left-tip {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: rgba(255,255,255,0.45); text-align: center;
            margin-top: 4px; line-height: 1.5;
        }
        .left-tip svg { width: 14px; height: 14px; stroke: rgba(255,255,255,0.35); flex-shrink: 0; }

        /* ── RIGHT PANEL ── */
        .right__section { overflow-y: auto; padding: 32px 28px; }

        /* ── PROGRESS BAR ── */
        .progress-top {
            display: flex; align-items: center; gap: 8px; margin-bottom: 28px;
            padding: 14px 18px; background: #f0fdf9; border-radius: 12px; border: 1px solid #ccfbf1;
        }
        .prog-step { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: #94a3b8; }
        .prog-step.active { color: #0d9488; }
        .prog-num {
            width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0;
            display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;
        }
        .prog-step.active .prog-num { background: #0d9488; color: white; }
        .prog-line { flex: 1; height: 2px; background: #e2e8f0; border-radius: 2px; }

        /* ── SECTION HEADERS ── */
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin: 28px 0 18px; padding-bottom: 12px; border-bottom: 2px solid #e8f5f3;
        }
        .section-header .icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, #0d9488, #5eead4);
            display: flex; align-items: center; justify-content: center;
        }
        .section-header .icon svg { width: 18px; height: 18px; stroke: white; }
        .section-header h3 { font-size: 15px; font-weight: 700; color: #1a3a4a; margin: 0; }
        .required-note { font-size: 12px; color: #64748b; margin-left: auto; }

        /* ── FILE UPLOAD ── */
        input[type="file"] {
            border: 2px dashed #cbd5e1; border-radius: 8px; padding: 10px 12px;
            width: 100%; cursor: pointer; font-size: 13px;
            background: #f8fafc; transition: border-color 0.2s;
        }
        input[type="file"]:hover { border-color: #0d9488; background: #f0fdf9; }

        /* ── SUBMIT BUTTON ── */
        .btn-submit-driver {
            background: linear-gradient(135deg, #0d9488, #0e6e5e);
            color: white; border: none; border-radius: 12px;
            padding: 16px 40px; font-size: 16px; font-weight: 700;
            cursor: pointer; width: 100%; margin-top: 24px;
            transition: all 0.3s; box-shadow: 0 4px 15px rgba(13,148,136,0.35);
        }
        .btn-submit-driver:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,148,136,0.5); }
    </style>
    @yield('css')
</head>

<body class="vertical-layout 1-column blank-page admin-site-2 cap__site" data-open="click"
    data-menu="vertical-menu-modern" data-col="1-column">

    <!-- BEGIN: Content-->
    <div class="content__page">
        <div class="content__alert">
            @if (Session::has('message_error'))
                <div class="alert alert-danger center">{{ Session::get('message_error') }}</div>
            @elseif (Session::has('message_success'))
                <div class="alert alert-success center">{{ Session::get('message_success') }}</div>
            @endif
        </div>
        <div class="section__register">
            <div class="left__section">
                <div class="content__logo-information">

                    {{-- Logo en tarjeta blanca para contraste --}}
                    <a href="{{ url('inicio') }}" class="logo-card">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="AnDre Logo">
                    </a>

                    {{-- Separador --}}
                    <div style="width:100%; height:1px; background: rgba(255,255,255,0.12);"></div>

                    <div class="left-welcome">
                        <h2>Únete como Conductor</h2>
                        <p>Completa tu registro y empieza a generar ingresos con AnDre hoy.</p>
                    </div>

                    <div class="left-steps">
                        {{-- Paso 1 activo --}}
                        <div class="left-step active">
                            <div class="step-num">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="step-label">Datos Personales y Vehículo</div>
                                <div style="font-size:11px; color:rgba(255,255,255,0.45); margin-top:2px;">Paso actual</div>
                            </div>
                        </div>
                        {{-- Paso 2 --}}
                        <div class="left-step">
                            <div class="step-num">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                                </svg>
                            </div>
                            <div class="step-label">Revisión del equipo AnDre</div>
                        </div>
                        {{-- Paso 3 --}}
                        <div class="left-step">
                            <div class="step-num">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="step-label">Cuenta Activa y lista</div>
                        </div>
                    </div>

                    <div class="left-tip">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        Tu información está protegida y solo será usada para verificar tu cuenta.
                    </div>
                </div>
            </div>
            <div class="right__section">
                <div class="scroll__int-section">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <script src="{{ asset('assets/admin/scripts/admin-2.js') }}"></script>
    <script src="{{ asset('assets/admin/scripts/master.js') }}"></script>

    @yield('script')
</body>
</html>
