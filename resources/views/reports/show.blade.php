<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $attempt->publicDisplayName() }} on CrazyExam</title>
    <meta property="og:title" content="{{ $attempt->reportHeadline() }}">
    <meta property="og:description" content="{{ $attempt->reportSubtitle() }}">
    <meta property="og:url" content="{{ $shareUrl }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            color: #e5e7eb;
            min-height: 100vh;
        }

        .report-page {
            align-items: center;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .report-card {
            background: #101820;
            border: 1px solid rgba(45, 212, 191, 0.35);
            border-radius: 0.75rem;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
            max-width: 520px;
            overflow: hidden;
            width: 100%;
        }

        .report-card-header {
            background: linear-gradient(135deg, #143d3a, #0f766e);
            padding: 1.5rem;
        }

        .report-brand {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 800;
        }

        .report-brand strong {
            color: #a7f3d0;
        }

        .report-title {
            color: #fff;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.05;
            margin: 1.25rem 0 0.4rem;
        }

        .report-card-body {
            padding: 1.5rem;
        }

        .report-stat-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: 1rem 0;
        }

        .report-stat {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.5rem;
            padding: 0.9rem;
        }

        .report-stat span {
            color: #9ca3af;
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .report-stat strong {
            color: #f8fafc;
            display: block;
            font-size: 1.6rem;
            line-height: 1.1;
            margin-top: 0.35rem;
        }

        .report-share {
            align-items: center;
            background: #f8fafc;
            border-radius: 0.6rem;
            color: #102a43;
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            padding: 1rem;
        }

        .report-share img {
            border-radius: 0.35rem;
            height: 132px;
            width: 132px;
        }

        .short-link {
            color: #0f766e;
            display: block;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .report-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            max-width: 520px;
            width: 100%;
        }

        .report-actions .btn {
            min-width: 145px;
        }

        .report-action-note {
            color: #94a3b8;
            font-size: 0.9rem;
            margin: 0;
            text-align: center;
        }

        @media (max-width: 520px) {
            .report-stat-grid,
            .report-share {
                grid-template-columns: 1fr;
            }

            .report-share {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @php
        $isOwner = auth()->check() && auth()->id() === $attempt->user_id;
    @endphp

    <main class="report-page">
        <article class="report-card" id="reportCard">
            <div class="report-card-header">
                <div class="report-brand"><strong>Crazy</strong>Exam</div>
                <h1 class="report-title">{{ $attempt->reportHeadline() }}</h1>
                <p class="mb-0">{{ $attempt->reportSubtitle() }}</p>
            </div>

            <div class="report-card-body">
                <div class="report-stat-grid">
                    <div class="report-stat">
                        <span>Score</span>
                        <strong>{{ $attempt->score }} / {{ $attempt->question_count }}</strong>
                    </div>
                    <div class="report-stat">
                        <span>Speed</span>
                        <strong>{{ $attempt->timeUsedLabel() }}</strong>
                    </div>
                    <div class="report-stat">
                        <span>Overall Position</span>
                        <strong>{{ $overallRank ? '#' . $overallRank : '-' }}</strong>
                    </div>
                    <div class="report-stat">
                        <span>This Week</span>
                        <strong>{{ $weeklyRank ? '#' . $weeklyRank : '-' }}</strong>
                    </div>
                </div>

                <p class="text-muted mb-0">
                    Completed {{ $attempt->submitted_at->format('M j, Y') }} on CrazyExam.
                </p>

                <div class="report-share">
                    <img src="{{ $qrCodeUrl }}" alt="QR code for this CrazyExam report card">
                    <div>
                        <strong>Challenge friends to beat this.</strong>
                        <span class="short-link">{{ $shareUrl }}</span>
                        <a href="{{ route('reports.take', $attempt->share_token) }}" class="btn btn-sm btn-success mt-3">Take This Combo</a>
                    </div>
                </div>
            </div>
        </article>

        @if($isOwner)
            <div class="report-actions">
                <button type="button" class="btn btn-success" id="shareReport">Share Card</button>
                <button type="button" class="btn btn-outline-light" id="downloadReport">Download Image</button>
                <button type="button" class="btn btn-outline-light" id="copyReportLink">Copy Link</button>
            </div>
            <p class="report-action-note" id="reportActionNote">Share the link, download the image, or do both.</p>
        @endif
    </main>

    @if($isOwner)
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
    <script>
        window.addEventListener('load', function () {
            var card = document.getElementById('reportCard');
            var shareButton = document.getElementById('shareReport');
            var downloadButton = document.getElementById('downloadReport');
            var copyButton = document.getElementById('copyReportLink');
            var note = document.getElementById('reportActionNote');
            var shareUrl = @json($shareUrl);
            var shareTitle = @json($attempt->reportHeadline());
            var shareText = @json($attempt->reportSubtitle() . '. Can you beat this score?');

            function setNote(message) {
                if (note) {
                    note.textContent = message;
                }
            }

            async function captureCard() {
                if (! window.html2canvas || ! card) {
                    return null;
                }

                return window.html2canvas(card, {
                    backgroundColor: '#0f172a',
                    scale: Math.min(2, window.devicePixelRatio || 1),
                    useCORS: true,
                });
            }

            function canvasToBlob(canvas) {
                return new Promise(function (resolve) {
                    canvas.toBlob(resolve, 'image/png', 0.96);
                });
            }

            if (shareButton) {
                shareButton.addEventListener('click', async function () {
                    if (! navigator.share) {
                        await navigator.clipboard.writeText(shareUrl);
                        setNote('Sharing is not available here, so the link was copied.');
                        return;
                    }

                    try {
                        var canvas = await captureCard();
                        var blob = canvas ? await canvasToBlob(canvas) : null;
                        var file = blob ? new File([blob], 'crazyexam-report-card.png', { type: 'image/png' }) : null;

                        if (file && navigator.canShare && navigator.canShare({ files: [file] })) {
                            await navigator.share({
                                title: shareTitle,
                                text: shareText,
                                url: shareUrl,
                                files: [file],
                            });
                        } else {
                            await navigator.share({
                                title: shareTitle,
                                text: shareText,
                                url: shareUrl,
                            });
                        }

                        setNote('Share sheet opened.');
                    } catch (error) {
                        setNote('Share was cancelled or blocked.');
                    }
                });
            }

            if (downloadButton) {
                downloadButton.addEventListener('click', async function () {
                    try {
                        var canvas = await captureCard();

                        if (! canvas) {
                            setNote('Image download is not available in this browser.');
                            return;
                        }

                        var link = document.createElement('a');
                        link.download = 'crazyexam-report-card.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        setNote('Report card image downloaded.');
                    } catch (error) {
                        setNote('Could not download the image. Try copying the link instead.');
                    }
                });
            }

            if (copyButton) {
                copyButton.addEventListener('click', async function () {
                    try {
                        await navigator.clipboard.writeText(shareUrl);
                        setNote('Short link copied.');
                    } catch (error) {
                        setNote(shareUrl);
                    }
                });
            }
        });
    </script>
    @endif
</body>
</html>
