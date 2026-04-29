<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct(mysqli $db)
    {
        $this->userModel = new UserModel($db);
    }


    private function popup(string $type, string $message, string $redirect): void
    {
        $_SESSION['popup_type']    = $type;
        $_SESSION['popup_message'] = $message;
        header("Location: $redirect");
        exit;
    }

    private function generateCsrf(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function generateLoginChallenge(): void
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $_SESSION['login_challenge'] = $code;
    }

    private function generateRegisterChallenge(): void
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $_SESSION['register_challenge'] = $code;
    }

    private function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            session_unset();
            session_destroy();
            session_start();
            $this->popup('csrf', 'Sesi keamanan kedaluwarsa. Silakan coba lagi.', 'index.php?page=login');
        }
        unset($_SESSION['csrf_token']);
    }

    public function showLogin(): void
    {
        $this->generateCsrf();
        $this->generateLoginChallenge();
        require __DIR__ . '/../views/auth/login.php';
    }

    public function showRegister(): void
    {
        $this->generateCsrf();
        $this->generateRegisterChallenge();
        require __DIR__ . '/../views/auth/register.php';
    }

    public function login(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=login");
            exit;
        }

        $this->verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $challengeInput = strtoupper(trim($_POST['login_challenge'] ?? ''));
        $challengeSaved = strtoupper(trim($_SESSION['login_challenge'] ?? ''));
        unset($_SESSION['login_challenge']);

        if (empty($email) || empty($password)) {
            $this->popup('error', 'Email dan password wajib diisi!', 'index.php?page=login');
        }

        if (empty($challengeSaved) || empty($challengeInput) || !hash_equals($challengeSaved, $challengeInput)) {
            $this->popup('error', 'Kode keamanan tidak cocok. Silakan coba lagi.', 'index.php?page=login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->popup('error', 'Format email tidak valid!', 'index.php?page=login');
        }


        $admin = $this->userModel->findAdminByEmail($email);
        if ($admin) {
            $stored = trim($admin['password']);
            if (password_verify($password, $stored) || $password === $stored) {
                if (!str_starts_with($stored, '$2y$')) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $this->userModel->getDb()->prepare("UPDATE admin SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $newHash, $admin['id']);
                    $stmt->execute();
                    $stmt->close();
                }
                session_regenerate_id(true);
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['nama']    = $admin['username'];
                $_SESSION['email']   = $admin['email'];
                $_SESSION['role']    = 'admin';
                header("Location: index.php?page=admin_dashboard");
                exit;
            }
        }


        $user = $this->userModel->findUserByEmail($email);
        if ($user) {
            $stored = trim($user['password']);
            if (password_verify($password, $stored) || $password === $stored) {
                if (!str_starts_with($stored, '$2y$')) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $this->userModel->getDb()->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $newHash, $user['id']);
                    $stmt->execute();
                    $stmt->close();
                }
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = 'user';
                header("Location: index.php?page=user_dashboard");
                exit;
            }
        }

        $this->popup('error', 'Email atau password yang kamu masukkan salah. Silakan periksa kembali dan coba lagi.', 'index.php?page=login');
    }

    public function register(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=register");
            exit;
        }

        $this->verifyCsrf();


        $captchaInput = strtoupper(trim($_POST['register_challenge'] ?? ''));
        $captchaSaved = strtoupper(trim($_SESSION['register_challenge'] ?? ''));
        unset($_SESSION['register_challenge']);

        if (empty($captchaSaved) || empty($captchaInput) || !hash_equals($captchaSaved, $captchaInput)) {
            $this->popup('error', 'Kode keamanan tidak cocok. Silakan coba lagi.', 'index.php?page=register');
        }

        $nama            = trim($_POST['nama'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $noTelp          = trim($_POST['no_telp'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($nama) || empty($email) || empty($noTelp) || empty($password)) {
            $this->popup('error', 'Semua field wajib diisi!', 'index.php?page=register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->popup('error', 'Format email tidak valid!', 'index.php?page=register');
        }

        if (strlen($password) < 8) {
            $this->popup('error', 'Password minimal 8 karakter!', 'index.php?page=register');
        }

        if ($password !== $confirmPassword) {
            $this->popup('error', 'Konfirmasi password tidak cocok!', 'index.php?page=register');
        }

        if ($this->userModel->findUserByEmail($email)) {
            $this->popup('error', 'Email sudah digunakan. Silakan gunakan email lain.', 'index.php?page=register');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        if ($this->userModel->createUser($nama, $email, $noTelp, $hashedPassword)) {
            $this->popup('success', 'Akun kamu berhasil dibuat! Silakan login menggunakan email dan password yang terdaftar.', 'index.php?page=register');
        }

        $this->popup('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.', 'index.php?page=register');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
