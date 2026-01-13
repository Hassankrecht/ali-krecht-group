@extends('layouts.app')

@section('title', 'Our Work Process')

@php
    $lang = app()->getLocale();
    $t = function ($en, $ar, $pt) use ($lang) {
        return match ($lang) {
            'ar' => $ar,
            'pt' => $pt,
            default => $en,
        };
    };

    $steps = [
        [
            'title' => $t('STEP 1 — Consultation & Site Visit', '١. الاستشارة وزيارة الموقع', 'PASSO 1 — Consulta e Visita ao Local'),
            'lines' => $t(
                [
                    'We start by understanding your goals, budget, timeline, and design preferences.',
                    'A site visit is scheduled to take exact measurements and evaluate existing conditions.',
                ],
                [
                    'نبدأ بفهم أهدافك، الميزانية، الجدول الزمني، وتفضيلات التصميم.',
                    'ثم نقوم بزيارة الموقع لفحص المكان وأخذ القياسات الدقيقة.',
                ],
                [
                    'Começamos entendendo seus objetivos, orçamento, cronograma e preferências de design.',
                    'Agendamos uma visita para medir e avaliar as condições existentes.',
                ]
            ),
        ],
        [
            'title' => $t('STEP 2 — Concept Design & Material Selection', '٢. التصميم الأولي واختيار المواد', 'PASSO 2 — Conceito e Seleção de Materiais'),
            'lines' => $t(
                ['Designers prepare concept drawings, moodboards, and material samples to define the visual direction.'],
                ['يقوم المصممون بإعداد تصور أولي، لوحات ألوان، وعينات مواد لتحديد الاتجاه الجمالي.'],
                ['Preparamos desenhos conceituais, moodboards e amostras de materiais para definir a direção visual.']
            ),
        ],
        [
            'title' => $t('STEP 3 — Detailed Drawings & BOQ', '٣. المخططات التفصيلية و BOQ', 'PASSO 3 — Desenhos Detalhados e BOQ'),
            'lines' => $t(
                ['Technical drawings, shop drawings, and a detailed Bill of Quantities for full accuracy.'],
                ['إعداد مخططات تنفيذية، شوب دروينغ، وقائمة كميات تفصيلية لضمان الشفافية والدقة.'],
                ['Desenhos técnicos, shop drawings e BOQ detalhado para total transparência.']
            ),
        ],
        [
            'title' => $t('STEP 4 — Manufacturing & Procurement', '٤. التصنيع وشراء المواد', 'PASSO 4 — Fabricação e Compras'),
            'lines' => $t(
                [
                    'Carpentry, custom furniture, and finishing materials enter production.',
                    'Certified materials are procured as per the approved BOQ.',
                ],
                [
                    'دخول النجارة والتفصيل والمواد الخاصة في مرحلة التصنيع.',
                    'شراء المواد المعتمدة حسب الـ BOQ.',
                ],
                [
                    'Marcenaria, móveis sob medida e materiais entram em produção.',
                    'Materiais são comprados conforme o BOQ aprovado.',
                ]
            ),
        ],
        [
            'title' => $t('STEP 5 — Execution & On-Site Work', '٥. التنفيذ والعمل الميداني', 'PASSO 5 — Execução em Obra'),
            'lines' => $t(
                ['Civil work, MEP, installation, fit-out, and finishing by specialized teams under supervision.'],
                ['تنفيذ الأعمال المدنية والميكانيكية والكهربائية والتركيبات والتشطيبات بإشراف هندسي دقيق.'],
                ['Obra civil, MEP, instalações e acabamentos sob supervisão rigorosa.']
            ),
        ],
        [
            'title' => $t('STEP 6 — Quality Check & Handover', '٦. الفحص النهائي والتسليم', 'PASSO 6 — Verificação de Qualidade e Entrega'),
            'lines' => $t(
                ['A full quality inspection is performed; adjustments are made; the project is handed over perfectly.'],
                ['نقوم بعملية فحص الجودة، ومعالجة أي ملاحظات، وتسليم المشروع بحالة ممتازة.'],
                ['Inspeção completa de qualidade e entrega pronta para uso.']
            ),
        ],
        [
            'title' => $t('STEP 7 — After-Service Support', '٧. دعم ما بعد التسليم', 'PASSO 7 — Suporte Pós-Entrega'),
            'lines' => $t(
                ['We stay available for maintenance, adjustments, and long-term support.'],
                ['نوفّر دعمًا مستمرًا وخدمات صيانة بعد التسليم.'],
                ['Oferecemos suporte contínuo e manutenção após a entrega.']
            ),
        ],
    ];
@endphp

@section('content')
    <div class="akg-hero-img-box position-relative">
        <img src="{{ asset('assets/img/services/hero.jpg') }}" class="akg-hero-img" alt="Process" loading="lazy">
        <div class="akg-hero-overlay"></div>
        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-2">
                {{ $t('Our Work Process – From Idea to Handover', 'عملية العمل – من الفكرة إلى التسليم', 'Nosso Processo – Da Ideia à Entrega') }}
            </h1>
            <p class="text-light small">
                {{ $t('Clear stages for premium results.', 'مراحل واضحة لنتائج فاخرة.', 'Etapas claras para resultados premium.') }}
            </p>
        </div>
    </div>

    <section class="container-xxl py-5">
        <div class="container akg-newcard p-4">
            <div class="text-center mb-5">
                <h2 class="akg-section-head">
                    {{ $t('How We Deliver Premium Projects', 'كيف ننفذ مشاريع فاخرة', 'Como entregamos projetos premium') }}
                </h2>
                <p class="text-muted">
                    {{ $t('Transparent stages, timelines, and quality checkpoints.', 'مراحل وجداول زمنية ونقاط جودة واضحة.', 'Etapas, prazos e qualidade transparentes.') }}
                </p>
            </div>

            <div class="row g-4">
                @foreach($steps as $index => $step)
                    <div class="col-lg-6">
                        <div class="akg-card h-100 p-4">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="badge bg-warning text-dark fs-6">{{ $index + 1 }}</div>
                                <h5 class="text-gold mb-0">{{ $step['title'] }}</h5>
                            </div>
                            @foreach($step['lines'] as $line)
                                <p class="text-muted mb-2" @if($lang==='ar') dir="rtl" class="text-end" @endif>{{ $line }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('contact') }}" class="btn btn-gold px-5 py-3 fw-bold">
                    {{ $t('Book a Consultation', 'احجز استشارة', 'Agendar consulta') }}
                </a>
            </div>
        </div>
    </section>
@endsection
