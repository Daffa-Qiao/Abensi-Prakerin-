<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\MemberModel;

;

class Auth extends BaseController
{
    protected $helpers = (['url', 'form', 'text']);
    protected $validation;
    protected $member;

    function __construct()
    {
        $this->validation = \Config\Services::validation();
        helper('global_fungsi_helper');
        helper('cookie');
        helper('text');
    }

    public function login()
    {
        if (get_cookie('cookie_username') && get_cookie('cookie_password')) {
            $username = get_cookie('cookie_username');
            $password = get_cookie('cookie_password');
            $user = new MemberModel();

            $dataAkun = $user->where('username', $username)->orWhere('email', $username)->first();
            if ($dataAkun != null) {
                if ($password == $dataAkun['password']) {
                    $dataSesi = [
                        'logged_in' => true,
                        'member_id' => $dataAkun['member_id'],
                        'member_username' => $dataAkun['username'],
                        'member_password' => $dataAkun['password'],
                        'member_email' => $dataAkun['email'],
                        'member_nama_lengkap' => $dataAkun['nama_lengkap'],
                        'member_nim_nis' => $dataAkun['nim_nis'],
                        'member_jenis_kelamin' => $dataAkun['jenis_kelamin'],
                        'member_no_hp' => $dataAkun['no_hp'],
                        'member_instansi' => $dataAkun['instansi_pendidikan'],
                        'member_nama_instansi' => $dataAkun['nama_instansi'],
                        'member_foto' => $dataAkun['foto'],
                    ];
                    session()->set($dataSesi);

                    if ($dataAkun['level'] == 'Super Admin') {
                        session()->set('redirected', 'superadmin');
                        notif_swal('success', 'Selamat Datang');
                        return redirect()->to('admin/dashboard');
                    } else if ($dataAkun['level'] == 'Admin') {
                        session()->set('redirected', 'admin');
                        notif_swal('success', 'Selamat Datang');
                        return redirect()->to('admin/dashboard');
                    } else if ($dataAkun['level'] == 'User') {
                        session()->set('redirected', 'user');
                        notif_swal('success', 'Selamat Datang');
                        return redirect()->to('user/my-profile');
                    }
                }
            }
        }
        $data = [
            'validation' => null
        ];
        return view('User/Auth/login', $data);
    }

    function loginProcess()
    {
        helper('cookie');
        $fieldType = filter_var($this->request->getVar('login_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $username = $this->request->getVar('login_id');
        $password = $this->request->getVar('password');

        if ($fieldType == 'username') {
            $rules = ($this->validate([
                'login_id' => [
                    'rules' => 'required|is_not_unique[member.username]',
                    'errors' => [
                        'required' => 'Username atau Email harus diisi',
                        'is_not_unique' => 'Username yang dimasukkan tidak terdaftar',
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password harus diisi',
                    ]
                ],

            ]));
        } else {
            $rules = ($this->validate([
                'login_id' => [
                    'rules' => 'required|is_not_unique[member.email]',
                    'errors' => [
                        'required' => 'Username atau Email harus diisi',
                        'is_not_unique' => 'Email yang dimasukkan tidak terdaftar',
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password harus diisi',
                    ]
                ],

            ]));
        }

        if (!$rules) {
            return view('User/Auth/login', [
                'validation' => $this->validator->getErrors(),
            ]);
        } else {
            // verifikasi captcha
            // $secret = getenv('SECRETKEY');

            // $credential = array(
            //     'secret' => $secret,
            //     'response' => $this->request->getVar('g-recaptcha-response')
            // );

            // $verify = curl_init();
            // curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            // curl_setopt($verify, CURLOPT_POST, true);
            // curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
            // curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec($verify);
            // curl_close($verify);

            // $status = json_decode($response, true);
            // if (!$status['success']) {
            //     return view('User/Auth/login', [
            //         'captcha' => 'Verifikasi CAPTCHA!'
            //     ]);
            // }

            $member = new MemberModel();
            $memberInfo = $member->where($fieldType, $username)->first();
            $memberPassword = $memberInfo['password'];

            if (($password != $memberPassword)) {
                session()->setFlashdata('login_id', $username);
                session()->setFlashdata('password', $password);
                $err = 'Password yang anda masukkan salah';
                return redirect()->back()->with('error', $err);
            }
            if (empty($err)) {
                if ($memberInfo['is_verifikasi'] != 'yes') {
                    session()->set('member_email', $memberInfo['email']);
                    $err = 'Akun anda belum diverifikasi, silahkan dapatkan OTP untuk verifikasi';
                    session()->setFlashdata('error', $err);
                    return redirect()->to('/verifikasi');
                }
            }

            if (empty($err)) {
                // membuat data session
                $dataSesi = [
                    'logged_in' => true,
                    'member_id' => $memberInfo['member_id'],
                    'member_username' => $memberInfo['username'],
                    'member_password' => $memberInfo['password'],
                    'member_email' => $memberInfo['email'],
                    'member_nama_lengkap' => $memberInfo['nama_lengkap'],
                    'member_nim_nis' => $memberInfo['nim_nis'],
                    'member_jenis_kelamin' => $memberInfo['jenis_kelamin'],
                    'member_no_hp' => $memberInfo['no_hp'],
                    'member_instansi' => $memberInfo['instansi_pendidikan'],
                    'member_nama_instansi' => $memberInfo['nama_instansi'],
                    'member_foto' => $memberInfo['foto'],
                ];
                session()->set($dataSesi);

                // membuat cookie login
                set_cookie('cookie_username', $username, 3600 * 24 * 30);
                set_cookie('cookie_password', $memberInfo['password'], 3600 * 24 * 30);

                if ($memberInfo['level'] == 'Super Admin') {
                    session()->set('redirected', 'superadmin');
                    notif_swal('success', 'Selamat Datang');
                    return redirect()->to('admin/dashboard')->withCookies();
                } else if ($memberInfo['level'] == 'Admin') {
                    session()->set('redirected', 'admin');
                    notif_swal('success', 'Selamat Datang');
                    return redirect()->to('admin/dashboard')->withCookies();
                } else if ($memberInfo['level'] == 'User') {
                    session()->set('redirected', 'user');
                    notif_swal('success', 'Selamat Datang');
                    return redirect()->to('user/my-profile')->withCookies();
                }
            }
        }
    }

    public function logout()
    {
        session()->destroy();
        delete_cookie('cookie_username');
        delete_cookie('cookie_password');
        /** Untuk session login */
        if (session()->get('logged_in') != '') {
            session()->setFlashdata('success', 'Berhasil Logout');
        }
        return view('User/Auth/login');
    }

    public function register()
    {
        $data = [
            'validation' => null
        ];
        return view('User/Auth/register', $data);
    }

    public function registerProcess()
    {
        $member = new MemberModel();
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $rules = $this->validate([
            'username' => [
                'rules' => 'required|is_unique[member.username]|regex_match[/^\S+$/]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'is_unique' => 'Username sudah terdaftar',
                    'regex_match' => 'Username tidak boleh menggunakan spasi'
                ]
            ],
            'email' => [
                'rules' => 'required|is_unique[member.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'is_unique' => 'Email sudah terdaftar',
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[5]|regex_match[/^\S+$/]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Minimum panjang password adalah 5 karakter',
                    'regex_match' => 'Password tidak boleh menggunakan spasi'
                ]
            ],
            'konfirmasi_password' => [
                'rules' => 'matches[password]',
                'errors' => [
                    'matches' => 'Konfirmasi Password tidak sesuai',
                ]
            ]
        ]);
        if (!$rules) {
            return view('User/Auth/register', [
                'validation' => [
                    'username' => $this->validator->getError('username'),
                    'email' => $this->validator->getError('email'),
                    'password' => $this->validator->getError('password'),
                    'konfirmasi_password' => $this->validator->getError('konfirmasi_password'),
                ]
            ]);
        } else {
            // verifikasi captcha
            $secret = getenv('SECRETKEY');

            $credential = array(
                'secret' => $secret,
                'response' => $this->request->getVar('g-recaptcha-response')
            );

            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            curl_close($verify);

            $status = json_decode($response, true);
            if (!$status['success']) {
                // session()->setFlashdata('error', 'Verifikasi CAPTCHA!');
                return view('User/Auth/register', [
                    'captcha' => 'Verifikasi CAPTCHA!'
                ]);
            }

            /**Membuat function kirim email verifikasi menggunakan helpers */
            $token = random_string('numeric', 6);
            $link = site_url("verifikasi/?email=$email&token=$token");
            $attachment = "";
            $to = "$email";
            $title = "Verifikasi Akun";
            $uniq_id = uniqid();

            $message = ' <p>Berikut ini <a style="text-decoration: none; font-weight: bold;">' . $token . '</a> kode OTP untuk melakukan verifikasi akun anda, atau klik tombol di bawah ini :</p>
                    <div style="text-align: center;">
                        <a href="' . $link . '" style="display: inline-block; padding: 10px 20px; background-color: #3498db; border-radius: 5px; text-decoration: none; color: white;">Verifikasi</a>
                    </div>
                    <hr style="border-top: 2px solid ; margin-top: 2rem;">
                    <h3 style="margin-top: 1rem;">CATATAN : Kode OTP akan kadaluwarsa dalam 15 menit. Harap segera gunakkan</h3>
                    <div style="display: none;">' . $uniq_id . '</div>';
            kirim_email($attachment, $to, $title, $message);

            /** Mendaftarkan akun ke database */
            $dataUpdate = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'token' => $token
            ];
            $member->insert($dataUpdate);

            // Membuat session user
            $dataSesi = [
                'member_email' => $email,
                'member_password' => $password,
            ];
            session()->set($dataSesi);
            /**Pesan sukses */
            session()->setFlashdata("success", "Berhasil register, kode OTP sudah dikirim ke Email anda");
            return redirect()->to('/verifikasi');
        }
    }


    public function verifikasi()
    {
        $token = $this->request->getVar('token');
        if ($this->request->getVar('email') != '') {
            $email = $this->request->getVar('email');
        } else if (session()->get('member_email')) {
            $email = session()->get('member_email');
        } else {
            return redirect()->to('login');
        }

        $user = new MemberModel();
        $dataAkun = $user->where('email', $email)->get()->getRowArray();

        if ($dataAkun['is_verifikasi'] == 'yes') {
            session()->set([
                'logged_in' => true,
                'member_id' => $dataAkun['member_id'],
                'member_username' => $dataAkun['username'],
                'member_password' => $dataAkun['password'],
                'member_email' => $dataAkun['email'],
                'member_nama_lengkap' => $dataAkun['nama_lengkap'],
                'member_nim_nis' => $dataAkun['nim_nis'],
                'member_jenis_kelamin' => $dataAkun['jenis_kelamin'],
                'member_no_hp' => $dataAkun['no_hp'],
                'member_instansi' => $dataAkun['instansi_pendidikan'],
                'member_nama_instansi' => $dataAkun['nama_instansi'],
                'member_foto' => $dataAkun['foto'],
            ]);
            if ($dataAkun['level'] == 'User') {
                session()->set('redirected', 'user');
            }
            session()->setFlashdata('sudah_verifikasi', true);
            return redirect()->to('login');
        }

        if ($dataAkun['is_verifikasi'] == 'pending') {
            $user->save([
                'member_id' => $dataAkun['member_id'],
                'token' => null
            ]);
            session()->set([
                'akun_username' => $dataAkun['username'],
                'member_email' => $dataAkun['email']
            ]);
            notif_swal_dua('info', 'Lengkapi data berikut untuk melanjutkan proses verifikasi');
            return redirect()->to('user/index');
        }

        if ($token != '') {
            if ($dataAkun['token'] == $token) {
                $user->save([
                    'member_id' => $dataAkun['member_id'],
                    'token' => null,
                    'is_verifikasi' => 'pending'
                ]);
                session()->set([
                    'akun_username' => $dataAkun['username'],
                    'member_email' => $dataAkun['email'],
                ]);
                notif_swal_dua('info', 'Lengkapi data berikut untuk melanjutkan proses verifikasi');
                return redirect()->to('user/index');
            }
        }
        return view('User/verifikasi');
    }

    public function verifikasiProcess()
    {
        if ($this->request->getVar('email') != '') {
            $email = $this->request->getVar('email');
        } else if (session()->get('member_email')) {
            $email = session()->get('member_email');
        }

        $token = $this->request->getVar('token');
        // if ($token == ''){
        //     session()->setFlashdata('error', 'Kode OTP tidak valid');
        //     return redirect()->back();
        // }

        $user = new MemberModel();
        $dataUser = $user->where('email', $email)->get()->getRowArray();

        if ($dataUser['token'] != $token) {
            session()->setFlashdata('error', 'Kode OTP tidak valid');
            return redirect()->back();
        } else {
            $user->save([
                'member_id' => $dataUser['member_id'],
                'token' => null,
                'is_verifikasi' => 'pending'
            ]);
            session()->set([
                'akun_username' => $dataUser['username'],
                'member_email' => $dataUser['email'],
            ]);
            notif_swal_dua('info', 'Lengkapi data berikut untuk melanjutkan proses verifikasi');
            return redirect()->to('user/index');
        }
    }

    public function kirim_ulang()
    {
        if ($this->request->getVar('email') != '') {
            $email = $this->request->getVar('email');
        } else if (session()->get('member_email')) {
            $email = session()->get('member_email');
        } else {
            return redirect()->to('login');
        }


        $user = new MemberModel();
        // mencari data user
        $dataUser = $user->where('email', $email)->get()->getRowArray();

        /**Membuat function kirim email verifikasi menggunakan helpers */
        $token = random_string('numeric', 6);
        $link = site_url("verifikasi/?email=$email&token=$token");
        $attachment = "";
        $to = "$email";
        $title = "Verifikasi Akun";
        $uniq_id = uniqid();

        $message = ' <p>Berikut ini <a style="text-decoration: none; font-weight: bold;">' . $token . '</a> kode OTP untuk melakukan verifikasi akun anda, atau klik tombol di bawah ini :</p>
                    <div style="text-align: center;">
                        <a href="' . $link . '" style="display: inline-block; padding: 10px 20px; background-color: #3498db; border-radius: 5px; text-decoration: none; color: white;">Verifikasi</a>
                    </div>
                    <hr style="border-top: 2px solid ; margin-top: 2rem;">
                    <h3 style="margin-top: 1rem;">CATATAN : Kode OTP akan kadaluwarsa dalam 15 menit. Harap segera gunakkan</h3>
                    <div style="display: none;">' . $uniq_id . '</div>';
        kirim_email($attachment, $to, $title, $message);

        // token sesuai dan lanjutkan proses verifikasi
        $user->save([
            'member_id' => $dataUser['member_id'],
            'token' => $token
        ]);
        // session()->setFlashdata('success', 'Kode OTP telah dikirim, silahkan cek email anda');
        // return redirect()->back();
        $hasil['sukses'] = true;
        return json_encode($hasil);
    }
    public function index()
    {
        $email = session()->get('member_email');
        $user = new MemberModel();
        $dataAkun = $user->where('email', $email)->get()->getRowArray();
        if ($dataAkun) {
            if ($dataAkun['is_verifikasi'] == 'yes') {
                session()->set([
                    'logged_in' => true,
                    'member_id' => $dataAkun['member_id'],
                    'member_username' => $dataAkun['username'],
                    'member_password' => $dataAkun['password'],
                    'member_email' => $dataAkun['email'],
                    'member_nama_lengkap' => $dataAkun['nama_lengkap'],
                    'member_nim_nis' => $dataAkun['nim_nis'],
                    'member_jenis_kelamin' => $dataAkun['jenis_kelamin'],
                    'member_no_hp' => $dataAkun['no_hp'],
                    'member_instansi' => $dataAkun['instansi_pendidikan'],
                    'member_nama_instansi' => $dataAkun['nama_instansi'],
                    'member_foto' => $dataAkun['foto'],
                ]);
                if ($dataAkun['level'] == 'User') {
                    session()->set('redirected', 'user');
                }
                session()->setFlashdata('sudah_verifikasi', true);
                return redirect()->to('login');
            }
        }
        $data = [
            'validation' => null,
        ];

        return view('User/lengkapi_data', $data);
    }

    public function indexHandler()
    {
        /**Merekam input dari user */
        $nama_lengkap = ucwords($this->request->getVar('nama_lengkap'));
        $nim_nis = $this->request->getVar('nim_nis');
        $no_hp = $this->request->getVar('no_hp');
        $gender = $this->request->getVar('gender');
        $instansi = $this->request->getVar('instansi');
        $nama_instansi = strtoupper($this->request->getVar('nama_instansi'));
        $fileProfile = $this->request->getFile('profile');

        if (is_null($fileProfile) || !$fileProfile->isValid()) {
            $namaFile = 'profile.png';
        } else {
            if ($fileProfile->isValid()) {
                $namaFile = $fileProfile->getName();
                $fileProfile->move(FCPATH . 'uploadFoto', $namaFile);
            }
        }

        /**Mengambil session */
        $email = session()->get('member_email');

        $member = new MemberModel();
        $memberInfo = $member->where('email', $email)->first();
        $rules = $this->validate([
            'nama_lengkap' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Lengkap harus diisi',
                ]
            ],
            'nim_nis' => [
                'rules' => 'required|is_unique[member.nim_nis]',
                'errors' => [
                    'required' => 'NIS/NIM harus diisi',
                    'is_unique' => 'NIS/NIM sudah terdaftar',
                ]
            ],
            'gender' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis Kelamin harus diisi'
                ]
            ],
            'instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Instansi Pendidikan harus diisi'
                ]
            ],
            'nama_instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Instansi harus diisi',
                ]
            ],
            'no_hp' => [
                'rules' => 'required|is_unique[member.no_hp]|numeric|regex_match[/^08\d{8,12}$/]',
                'errors' => [
                    'required' => 'Nomor Telepon harus diisi',
                    'numeric' => 'Nomor Telepon hanya boleh berisi angka',
                    'is_unique' => 'Nomor Telepon sudah terdaftar',
                    'regex_match' => 'Nomor Telepon tidak valid'
                ]
            ]
        ]);


        if (!$rules) {
            return view('User/lengkapi_data', [
                'validation' => $this->validator->getErrors()
            ]);
        } else {
            $dataUpdate = [
                'nama_lengkap' => $nama_lengkap,
                'nim_nis' => $nim_nis,
                'jenis_kelamin' => $gender,
                'no_hp' => $no_hp,
                'foto' => $namaFile,
                'instansi_pendidikan' => $instansi,
                'nama_instansi' => $nama_instansi,
                'is_verifikasi' => 'yes'
            ];
            $member->where('email', $email)->set($dataUpdate)->update();
            session()->remove('akun_username');
            $dataSesi = [
                'logged_in' => true,
                'redirected' => 'user',
                'member_id' => $memberInfo['member_id'],
                'member_username' => $memberInfo['username'],
                'member_email' => $memberInfo['email'],
                'member_nama_lengkap' => $nama_lengkap,
                'member_no_hp' => $no_hp,
                'member_foto' => $namaFile,
                'member_jenis_kelamin' => $gender,
                'member_nim_nis' => $nim_nis,
                'member_instansi' => $instansi,
                'member_nama_instansi' => $nama_instansi,
            ];
            session()->set($dataSesi);
            // membuat cookie login
            set_cookie('cookie_username', $memberInfo['username'], 3600 * 24 * 30);
            set_cookie('cookie_password', $memberInfo['password'], 3600 * 24 * 30);

            notif_swal('success', 'Berhasil Verifikasi Akun');
            return redirect()->to('user/my-profile')->withCookies();
        }
    }

    public function forgetPassword()
    {
        if ($this->request->getMethod() == 'post') {
            $fieldType = filter_var($this->request->getVar('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $userInput = $this->request->getVar('email');

            if ($userInput == '') {
                $err = 'Masukkan Username atau Email untuk melakukan reset password';
            }

            if (empty($err)) {
                if ($fieldType == 'email') {
                    $member = new MemberModel();
                    $memberInfo = $member->where($fieldType, $userInput)->first();
                    if (!$memberInfo) {
                        $err = 'Email yang dimasukkan tidak terdaftar';
                    }
                }
            }

            if (empty($err)) {
                if ($fieldType == 'username') {
                    $member = new MemberModel();
                    $memberInfo = $member->where($fieldType, $userInput)->first();
                    if (!$memberInfo) {
                        $err = 'Username yang dimasukkan tidak terdaftar';
                    }
                }
            }

            if (empty($err)) {
                $email = $memberInfo['email'];
                /**Membuat function kirim email verifikasi menggunakan helpers */
                $token = random_string('numeric', 6);
                $link = site_url("resetpassword/?email=$email&token=$token");
                $attachment = "";
                $to = "$email";
                $title = "Reset Password";
                $uniq_id = uniqid();

                $message = '<p>Berikut ini link untuk melakukan reset password anda, klik tombol di bawah ini :</p>
                    <div style="text-align: center;">
                        <a href="' . $link . '" style="display: inline-block; padding: 10px 20px; background-color: #3498db; border-radius: 5px; text-decoration: none; color: white;">Reset Password</a>
                    </div>
                    <hr style="border-top: 2px solid ; margin-top: 2rem;">
                    <h3 style="margin-top: 1rem;">CATATAN : Link Reset Password akan kadaluwarsa dalam 15 menit. Harap segera gunakkan</h3>
                    <div style="display: none;">' . $uniq_id . '</div>';
                kirim_email($attachment, $to, $title, $message);

                $dataUpdate = [
                    'email' => $memberInfo['email'],
                    'token' => $token
                ];
                $member->where('email', $email)->set($dataUpdate)->update();

                session()->set('email', $email);
                session()->setFlashdata('success', 'Link reset password telah dikirim ke email anda');
                return redirect()->back();
            }

            if ($err) {
                session()->setFlashdata('email', $userInput);
                session()->setFlashdata('invalid', 'is-invalid');
                session()->setFlashdata('error', $err);
                return redirect()->back();
            }
        }


        return view('User/Auth/forgetPassword');
    }

    public function resetPassword()
    {
        $token = $this->request->getVar('token');
        $email = $this->request->getVar('email');
        if ($email == '' or $token == '') {
            $err = 'Link reset password error, silahkan dapatkan link kembali';
            session()->setFlashdata('error', $err);
            return redirect()->to('forgetpassword');
        }

        $data = [
            'validation' => null
        ];
        echo view('User/Auth/resetPassword', $data);
    }

    function resetPasswordProcess()
    {
        $token = $this->request->getVar('token');
        $email = $this->request->getVar('email');

        $member = new MemberModel();
        $memberInfo = $member->where('email', $email)->first();
        $dataToken = $memberInfo['token'];

        if ($dataToken != $token) {
            $err = 'Link reset password error, silahkan dapatkan link kembali';
            session()->setFlashdata('error', $err);
            return redirect()->to('/forgetpassword');
        }

        $rules = $this->validate([
            'password' => [
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Minimum panjang untuk Password adalah 5 karakter'
                ]
            ],
            'konfirmasi_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi Password tidak sesuai',
                    'matches' => 'Konfirmasi Password harus sama dengan Password'
                ]
            ]
        ]);
        if (!$rules) {
            return view('User/Auth/resetPassword', [
                'validation' => $this->validator->getErrors()
            ]);
        } else {
            $member = new MemberModel();
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $dataUser = $member->where('email', $email)->first();
            if ($password == $dataUser['password']) {
                session()->setFlashdata('error', 'Password sudah digunakkan');
                return redirect()->back();
            }
            $dataUpdate = [
                'password' => $password,
                'token' => null
            ];
            $member->where('email', $email)->set($dataUpdate)->update();
            session()->remove('email');
            session()->setFlashdata('success', 'Password berhasil direset, silahkan login');
            return redirect()->to('/login');
        }
    }
}