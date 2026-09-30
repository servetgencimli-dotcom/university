<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Davamiyyət Kiosku — {{ $section->course->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #030712;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .kiosk-header {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(12px);
        }
        .kiosk-main {
            flex: 1;
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 2rem;
            padding: 2rem;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
        }
        .qr-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(16px);
            text-align: center;
        }
        .qr-wrapper {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
            margin: 1.5rem 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 10px #34d399;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(52, 211, 153, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }
        .live-students-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(16px);
            display: flex;
            flex-direction: column;
        }
        .progress-bar-bg {
            height: 14px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0 1.5rem 0;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            width: 0%;
            transition: width 0.5s ease;
        }
        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            max-height: 520px;
            padding-right: 0.5rem;
        }
        .student-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }
        .student-card.present {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #94a3b8;
            font-size: 1rem;
        }
        .student-card.present .avatar {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }
    </style>
</head>
<body>

    {{-- Kiosk Header --}}
    <header class="kiosk-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 1.5rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); padding: 0.5rem 0.75rem; border-radius: 12px;">📱</div>
            <div>
                <h1 style="font-size: 1.35rem; font-weight: 700; color: #fff;">{{ $section->course->name }} ({{ $section->section_code }})</h1>
                <div style="font-size: 0.85rem; color: #94a3b8;">
                    Dərs Tarixi: {{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }} | Müəllim: {{ $section->teacher->name ?? Auth::user()->name }}
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <form action="{{ route('portal.qr.session.create', $section->id) }}" method="POST">
                @csrf
                <button type="submit" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    🔄 QR Kodu Yenilə
                </button>
            </form>
            <a href="{{ route('portal.teacher.schedule') }}" style="background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; text-decoration: none;">
                🚪 Kiosku Bağla
            </a>
        </div>
    </header>

    {{-- Kiosk Main Content --}}
    <div class="kiosk-main">

        {{-- QR Display Panel --}}
        <div class="qr-card">
            <div class="pulse-badge">
                <div class="pulse-dot"></div>
                <span>Canlı QR Davamiyyət Aktivdir</span>
            </div>

            <div class="qr-wrapper">
                <div id="qrcode"></div>
            </div>

            <div style="font-size: 0.9rem; color: #cbd5e1; margin-bottom: 0.5rem;">
                Mobil telefonunuzla QR kodu skan edin
            </div>

            <div style="font-size: 0.75rem; color: #64748b; background: rgba(0,0,0,0.3); padding: 0.5rem 1rem; border-radius: 8px; font-family: monospace; word-break: break-all;">
                Link: <span id="scanUrlDisplay"></span>
            </div>
        </div>

        {{-- Live Checked In List Panel --}}
        <div class="live-students-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #fff;">👥 Canlı Qrup İştirakçısı Siyahısı</h2>
                    <div style="font-size: 0.85rem; color: #94a3b8;">Avtonom real-vaxt sinxronizasiyası</div>
                </div>
                <div style="text-align: right;">
                    <span id="presentRatioText" style="font-size: 1.5rem; font-weight: 800; color: #34d399;">0 / {{ $section->enrollments->count() }}</span>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Tələbə Daxil Olub</div>
                </div>
            </div>

            <div class="progress-bar-bg">
                <div id="progressBarFill" class="progress-bar-fill"></div>
            </div>

            <div id="studentGrid" class="student-grid">
                {{-- Loaded via JS live status --}}
            </div>
        </div>

    </div>

    <script>
        const sessionCode = "{{ $session->session_code }}";
        const sessionId = "{{ $session->id }}";
        const scanUrl = "{{ route('portal.qr.scan', $session->session_code) }}";
        const statusUrl = "{{ route('portal.qr.status', $session->id) }}";

        document.getElementById('scanUrlDisplay').innerText = scanUrl;

        // Render QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: scanUrl,
            width: 240,
            height: 240,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Live status fetch function
        async function updateLiveStatus() {
            try {
                const response = await fetch(statusUrl);
                const data = await response.json();

                // Update counts
                document.getElementById('presentRatioText').innerText = `${data.present_count} / ${data.total_students}`;
                const pct = data.total_students > 0 ? (data.present_count / data.total_students) * 100 : 0;
                document.getElementById('progressBarFill').style.width = `${pct}%`;

                // Update student grid
                const grid = document.getElementById('studentGrid');
                grid.innerHTML = '';

                data.students.forEach(student => {
                    const card = document.createElement('div');
                    card.className = `student-card ${student.is_present ? 'present' : ''}`;
                    card.innerHTML = `
                        <div class="avatar">${student.name.charAt(0).toUpperCase()}</div>
                        <div style="overflow: hidden;">
                            <div style="font-weight: 600; color: #fff; font-size: 0.9rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">${student.name}</div>
                            <div style="font-size: 0.75rem; color: ${student.is_present ? '#34d399' : '#64748b'};">
                                ${student.is_present ? '🟢 Qeydə alındı' : '⚪ Hələ gəlməyib'}
                            </div>
                        </div>
                    `;
                    grid.appendChild(card);
                });

            } catch (err) {
                console.error("Live status update failed:", err);
            }
        }

        // Initial fetch and poll every 3 seconds
        updateLiveStatus();
        setInterval(updateLiveStatus, 3000);
    </script>
</body>
</html>
