<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password Akun ANAMTA</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f6f9fc;
            color: #1c2e4a;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        /* HEADER */
        .email-header {
            background: linear-gradient(90deg, #004C97 0%, #005BBB 50%, #E5B80B 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .email-header img {
            width: 230px;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        /* BODY */
        .email-body {
            padding: 36px;
        }

        h2 {
            color: #003B7A;
            font-size: 22px;
            margin-top: 0;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            color: #333;
        }

        /* BUTTON */
        .email-button {
            display: inline-block;
            background: linear-gradient(90deg, #005BBB 0%, #0071C1 50%, #E5B80B 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 15px;
            letter-spacing: 0.4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .email-button:hover {
            background: linear-gradient(90deg, #E5B80B 0%, #0071C1 50%, #005BBB 100%);
            transform: translateY(-2px);
        }

        /* FOOTER */
        .email-footer {
            background-color: #f4f7fb;
            text-align: center;
            padding: 18px;
            font-size: 12px;
            color: #6c757d;
        }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            .email-body {
                padding: 24px;
            }

            .email-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="email-header">
            <img src="{{ $message->embed(public_path('images/Logo Anamta Memanjang.avif')) }}" alt="Logo ANAMTA">
        </div>

        <div class="email-body">
            <h2>Reset Password Akun ANAMTA</h2>

            <p>Halo tim <strong>ANAMTA</strong>,</p>
            <p>Sistem menerima permintaan reset password untuk akun dengan username:
                <strong>{{ $username }}</strong>.
            </p>

            <p>Klik tombol di bawah ini untuk mengatur ulang kata sandi akun tersebut:</p>

            <p style="text-align:center; margin: 28px 0;">
                <a href="{{ $resetUrl }}" class="email-button">🔐 Reset Password</a>
            </p>

            <p style="font-size:13px; color:#777;">
                Jika Anda tidak meminta reset password, abaikan pesan ini.
                Link reset akan kedaluwarsa dalam <strong>60 menit</strong>.
            </p>
        </div>

        <div class="email-footer">
            © {{ date('Y') }} PT Annur Maknah Wisata (ANAMTA) – Sistem Inventory Perlengkapan Operasional
        </div>
    </div>

</body>

</html>
