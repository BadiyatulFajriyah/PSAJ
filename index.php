<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pilihan</title>
    <style>
        /* Global Styles */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgb(227, 243, 238);
            margin: 0;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        .container {
            text-align: center;
            width: 90%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.8); /* Transparan elegan */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px); /* Efek kaca */
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #2c3e50;
        }

        p {
            font-size: 1.4rem;
            margin-bottom: 20px;
            color: #34495e;
        }

        .btn {
            display: block;
            width: 100%;
            max-width: 550px; /* Lebih panjang */
            padding: 18px;
            margin: 12px auto;
            background-color: #618B7D;
            color: white;
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn:hover {
            background-color: #4a6355;
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1.3rem;
            }

            .btn {
                font-size: 1.2rem;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }

            p {
                font-size: 1.1rem;
            }

            .btn {
                font-size: 1rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WELCOME<br> KOPERASI SIJAM</h1>
        <p>LOGIN SEBAGAI</p>
        <button class="btn" onclick="window.location.href='login/login-admin.php'">ADMIN</button>
        <button class="btn" onclick="window.location.href='login/login-anggota.php'">ANGGOTA</button>
    </div>
</body>
</html>
