<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\Absensi;
use App\Validations\CustomRules;


class User extends BaseController
{
    protected $member;
    protected $absensi;
    protected $helpers = (['url', 'form', 'global_fungsi_helper']);

    function __construct()
    {
        $pager = \Config\Services::pager();
    }

    public function index()
    {

        $data['halaman'] = 'User | My Profile';
        $data['title'] = 'My Profile';
        $data['aktif_profile'] = 'aktif';
        $data['namaFile'] = session()->get('member_foto');
        echo view('User/v_my-profile', $data);
    }
    public function attendance()
    {
        $data['halaman'] = 'User | Attendance';
        $data['title'] = 'Attendance';
        $data['aktif_attendance'] = 'aktif';
        $data['namaFile'] = session()->get('member_foto');
        return view('User/v_attendance', $data);
    }

    public function attendanceProcess()
    {
        $absensi = new Absensi();
        $member = new MemberModel();
        $sesi_nis = session()->get('member_nim_nis');
        $memberInfo = $member->where('nim_nis', $sesi_nis)->first();
        $absensiInfo = $absensi->where('nim_nis', $sesi_nis)->orderBy('id', 'desc')->first();

        $lat = $this->request->getVar('lat');
        $long = $this->request->getVar('long');
        $today = date('Y-m-d');
        $currentTime = date('H:i');
        $foto = $this->request->getPost('photostore');

        if ($this->request->getVar('checkin')) {
            if ($currentTime < '06:00:00') {
                notif_swal('error', 'Belum masuk waktu absen');
                return redirect()->back();
            }
            if ($currentTime > '10:00:00') {
                notif_swal('error', 'Melewati batas waktu absen');
                return redirect()->back();
            } else {
                $encoded_data = $_POST['photoStore'];
                if ($encoded_data == null) {
                    notif_swal('error', 'Harus absen dengan foto');
                    return redirect()->back();
                }
                if ($absensiInfo != null) {
                    if ($absensiInfo['waktu_absen'] == $today) {
                        notif_swal('error', 'Anda sudah checkin hari ini');
                        return redirect()->back();
                    }
                }
                if ($lat == '' && $long == '') {
                    notif_swal('error', 'Aktifkan GPS / Lokasi anda');
                    return redirect()->back();
                }

                $binary_data = base64_decode($encoded_data, true);
                $photoname = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
                $lokasi = ('lat: ' . $lat . ', long: ' . $long);
                file_put_contents(FCPATH . 'uploadFoto/' . $photoname, $binary_data);

                $updateData = [
                    'nama_lengkap' => $memberInfo['nama_lengkap'],
                    'email' => $memberInfo['email'],
                    'instansi_pendidikan' => $memberInfo['instansi_pendidikan'],
                    'nama_instansi' => $memberInfo['nama_instansi'],
                    'nim_nis' => $memberInfo['nim_nis'],
                    'lokasi' => $lokasi,
                    'status' => 'Masuk',
                    'keterangan' => '',
                    'checkin_time' => $currentTime,
                    'foto_profile' => $memberInfo['foto'],
                    'foto_absen' => $photoname,
                    'jenis_user' => $memberInfo['jenis_user'],
                ];
                $absensi->insert($updateData);
                notif_swal_tiga('success', $currentTime, 'Berhasil Check-in');
                return redirect()->to('user/attendance');
            }
        }

        if ($this->request->getVar('checkout')) {
            if ($currentTime < '14:00:00') {
                notif_swal('error', 'Belum masuk waktu checkout');
                return redirect()->back();
            }
            if ($absensiInfo == null) {
                notif_swal('error', 'Anda belum checkin hari ini');
                return redirect()->back();
            }
            if ($absensiInfo['checkout_time'] != null) {
                notif_swal('error', 'Anda sudah checkout hari ini');
                return redirect()->back();
            }
            $encoded_data = $_POST['photoStore'];
            if ($encoded_data == null) {
                notif_swal('error', 'Harus absen dengan foto');
                return redirect()->back();
            }
            $binary_data = base64_decode($encoded_data, true);
            $photoname = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
            $lokasi = ('lat: ' . $lat . ', long: ' . $long);
            file_put_contents(FCPATH . 'uploadFoto/' . $photoname, $binary_data); 
            $updateData = [
                'checkout_time' => $currentTime,
            ];
            if ($currentTime >= '17:00:00') {
                $absensi->where('nim_nis', $sesi_nis)->orderBy('id', 'desc')->set($updateData)->update();
            }
            $absensi->where('nim_nis', $sesi_nis)->orderBy('id', 'desc')->set($updateData)->update();
            notif_swal_tiga('success', 'Berhasil Check-out', $currentTime);
            return redirect()->back();
        }


    }
    public function permission()
    {
        $data['halaman'] = 'User | Permission';
        $data['title'] = 'Permission';
        $data['aktif_permission'] = 'aktif';
        $data['namaFile'] = session()->get('member_foto');
        return view('User/v_permission', $data);
    }
    public function permissionProcess()
    {
        $absensi = new Absensi();
        $member = new MemberModel();
        $sesi_nis = session()->get('member_nim_nis');
        $memberInfo = $member->where('nim_nis', $sesi_nis)->first();
        $absensiInfo = $absensi->where('nim_nis', $sesi_nis)->orderBy('id', 'desc')->first();

        $status = $this->request->getVar('menu');
        $keterangan = $this->request->getVar('keterangan');
        $lat = $this->request->getVar('lat');
        $long = $this->request->getVar('long');
        $lokasi = ('lat: ' . $lat . ', long: ' . $long);
        $fotoAbsen = $this->request->getFile('foto_absen');

        $today = date('Y-m-d');
        $currentTime = date('H:i');

        if ($absensiInfo != null) {
            if ($absensiInfo['waktu_absen'] == $today) {
                notif_swal('error', 'Anda sudah checkin hari ini');
                return redirect()->back();
            }
        }
        if ($fotoAbsen == '') {
            notif_swal('error', 'Foto Tidak Boleh Kosong');
            return redirect()->back();
        }
        if ($lat == '' && $long == '') {
            notif_swal('error', 'Aktifkan GPS / Lokasi anda');
            return redirect()->back();
        }
        if ($keterangan == '' or $status == '') {
            notif_swal('error', 'Status dan Keterangan Tidak Boleh Kosong');
            return redirect()->back();
        } else {
            $namaFile = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
            $fotoAbsen->move(FCPATH . 'uploadFoto/', $namaFile);

            $updateData = [
                'nama_lengkap' => $memberInfo['nama_lengkap'],
                'email' => $memberInfo['email'],
                'instansi_pendidikan' => $memberInfo['instansi_pendidikan'],
                'nama_instansi' => $memberInfo['nama_instansi'],
                'nim_nis' => $memberInfo['nim_nis'],
                'lokasi' => $lokasi,
                'status' => $status,
                'keterangan' => $keterangan,
                'checkin_time' => $currentTime,
                'foto_profile' => $memberInfo['foto'],
                'foto_absen' => $namaFile,
            ];
            $absensi->insert($updateData);
            notif_swal_tiga('success', $currentTime, 'Berhasil Absen ' . $status);
            return redirect()->back();
        }

    }
    public function history()
    {
        $absensi = new Absensi();
        $user = new MemberModel();
        $sesi_nim = session()->get('member_nim_nis');
        $jumlahBaris = 10;
        $currentPage = $this->request->getVar('page_absensi');
        $dataAbsen = $absensi->where('nim_nis', $sesi_nim)->orderBy('id', 'desc')->paginate($jumlahBaris, 'absensi');
        $nama_lengkap = [];
        foreach ($dataAbsen as $v) {
            $nimUser = $v['nim_nis'];
            $dataUser = $user->where('nim_nis', $nimUser)->get()->getRowArray();
            $nama_lengkap = $dataUser ? $dataUser['nama_lengkap'] : '';
        }
        $data = [
            'halaman' => 'User | Hitory Absen',
            'title' => 'History Absen',
            'namaFile' => session()->get('member_foto'),
            'aktif_history' => 'aktif',
            'dataAbsen' => $dataAbsen,
            'pager' => $absensi->pager,
            'nomor' => nomor($currentPage, $jumlahBaris),
            'nama_lengkap' => $nama_lengkap
        ];
        return view('User/v_history', $data);
    }
    public function setting()
    {
        $sesi_id = session()->get('member_id');
        $member = new MemberModel();
        $data = $member->where('member_id', $sesi_id)->first();
        $email = $data['email'];
        $username = $data['username'];
        $data = [
            'validation' => null,
            'username' => $username,
            'email' => $email,
            'halaman' => 'User  | Setting & Privacy',
            'title' => 'Setting & Privacy',
            'namaFile' => session()->get('member_foto'),
            'aktif_setting' => 'aktif',
        ];
        return view('User/v_setting', $data);
    }
    public function settingProcess()
    {
        $email = $this->request->getVar('email');
        $username = $this->request->getVar('username');
        $password_new = $this->request->getVar('password_new');
        $file = $this->request->getFile('foto_profile');

        $sesi_email = session()->get('member_email');
        $sesi_username = session()->get('member_username');
        $sesi_id = session()->get('member_id');

        /**JIka tidak ada yang diubah maka akan di redirect kembali tanpa mengubah data apapun */
        if ($username == $sesi_username and $email == $sesi_email and $password_new == '') {
            if ($file != '' && $file->isValid()) {
                $member = new MemberModel();
                $dataUser = $member->find($sesi_id);
                $foto_lama = (FCPATH . 'uploadFoto/' . $dataUser['foto']);
                if (file_exists($foto_lama)) {
                    if ($dataUser['foto'] != 'profile.png' && $dataUser['foto'] != '') {
                        unlink($foto_lama);
                    }
                }

                $namaFile = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
                $file->move(FCPATH . 'uploadFoto', $namaFile);

                $update = [
                    'foto' => $namaFile,
                ];
                $member->where('member_id', $sesi_id)->set($update)->update();
                // update sesi foto
                session()->set('member_foto', $namaFile);
                notif_swal('success', 'Berhasil Update Data');
            }
            return redirect()->back();
        }

        if ($email != $sesi_email) {
            $rules = $this->validate([
                'email' => [
                    'rules' => 'required|is_unique[member.email, member_id, ' . $sesi_id . ']|valid_email',
                    'errors' => [
                        'required' => 'Email harus diisi',
                        'is_unique' => 'Email yang dimasukkan sudah terdaftar',
                        'valid_email' => 'Email tidak valid'
                    ]
                ],
                'password_old' => [
                    'rules' => 'required|check_old_password[password_old]',
                    'errors' => [
                        'required' => 'Current Password harus diisi',
                        'check_old_password' => 'Current Password tidak sesuai'
                    ]
                ],
            ]);
        }

        if ($username != $sesi_username) {
            $rules = $this->validate([
                'username' => [
                    'rules' => 'required|is_unique[member.username, member_id,' . $sesi_id . ']|regex_match[/^\S+$/]',
                    'errors' => [
                        'required' => 'Username harus diisi',
                        'is_unique' => 'Username yang dimasukkan sudah terdaftar',
                        'regex_match' => 'Username tidak boleh menggunakan spasi'
                    ]
                ]
            ]);
        }


        /**Validasi password */
        if ($password_new != '') {
            $rules = $this->validate([
                'password_old' => [
                    'rules' => 'required|check_old_password[password_old]',
                    'errors' => [
                        'required' => 'Current Password harus diisi',
                        'check_old_password' => 'Current Password tidak sesuai'
                    ]
                ],
                'password_new' => [
                    'rules' => 'min_length[5]|regex_match[/^\S+$/]',
                    'errors' => [
                        'min_length' => 'Minimun panjang password adalah 5 karakter',
                        'regex_match' => 'Password tidak boleh menggunakan spasi'
                    ]
                ],
                'konfirmasi_password_new' => [
                    'rules' => 'matches[password_new]',
                    'errors' => [
                        'matches' => 'Konfirmasi Password tidak sesuai'
                    ]
                ]
            ]);
        }
        /**JIka ada validasi yang error makan akan mengeluarkan errors nya */
        if (!$rules) {
            return view('User/v_setting', [
                'validation' => $this->validator->getErrors(),
                'halaman' => 'User  | Setting & Privacy',
                'title' => 'Setting & Privacy',
                'namaFile' => session()->get('member_foto'),
                'aktif_setting' => 'aktif',
            ]);
        } else {
            /**JIka proses validasi tidak ditemukan error maka akan mengupdate sesuai data yang diubah oleh user  */
            $member = new MemberModel();

            /** Jika password diubah maka akan melakukan update */
            if ($password_new != '') {
                $dataUpdate = [
                    'password' => $password_new
                ];
                $member->where('member_id', $sesi_id)->set($dataUpdate)->update();
                notif_swal('success', 'Berhasil Update Data');
            }

            if ($file != '' && $file->isValid()) {
                $member = new MemberModel();
                $dataUser = $member->find($sesi_id);
                $foto_lama = (FCPATH . 'uploadFoto/' . $dataUser['foto']);
                if (file_exists($foto_lama)) {
                    if ($dataUser['foto'] != 'profile.png' && $dataUser['foto'] != '') {
                        unlink($foto_lama);
                    }
                }

                $namaFile = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
                $file->move(FCPATH . 'uploadFoto', $namaFile);
                $update = [
                    'foto' => $namaFile,
                ];
                $member->where('member_id', $sesi_id)->set($update)->update();
                // update sesi foto
                session()->set('member_foto', $namaFile);
                notif_swal('success', 'Berhasil Update Data');
            }

            $member->save([
                'member_id' => $sesi_id,
                'username' => $username,
                'email' => $email,
            ]);
            session()->set([
                'member_username' => $username,
                'member_email' => $email,
            ]);
            notif_swal('success', 'Berhasil Update Data');

            return redirect()->back();
        }
    }
}