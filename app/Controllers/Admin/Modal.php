<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\Absensi;
use App\Models\Instansi;

class Modal extends BaseController
{
    protected $validation;
    protected $helpers = (['url', 'form']);
    function __construct()
    {
        $this->validation = \Config\Services::validation();
        helper("cookie");
        helper("global_fungsi_helper");
    }

    public function tambahUser()
    {
        $validasi = \Config\Services::validation();
        $aturan = [
            'nama_lengkap' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Lengkap harus diisi',
                ]
            ],
            'nim_nis' => [
                'rules' => 'required|is_unique[member.nim_nis]|numeric',
                'errors' => [
                    'required' => 'NIM/NIS harus diisi',
                    'is_unique' => 'NIM/NIS sudah terdaftar',
                    'numeric' => 'NIM/NIS hanya boleh berisi angka',
                ]
            ],
            'username' => [
                'rules' => 'required|is_unique[member.username]|regex_match[/^\S+$/]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'is_unique' => 'Username sudah terdaftar',
                    'regex_match' => 'Username tidak boleh menggunakan spasi'
                ]
            ],
            'gender' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis Kelamin harus diisi',
                ]
            ],
            'no_hp' => [
                'rules' => 'required|is_unique[member.no_hp]|regex_match[/^08\d{8,12}$/]',
                'errors' => [
                    'required' => 'Nomor Telepon harus diisi',
                    'is_unique' => 'Nomor Telepon sudah terdaftar',
                    'regex_match' => 'Nomor Telepon tidak valid'
                ]
            ],
            'email' => [
                'rules' => 'required|is_unique[member.email]|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'is_unique' => 'Email sudah terdaftar',
                    'valid_email' => 'Email tidak valid'
                ]
            ],
            'instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Instansi Pendidikan harus diisi',
                ]
            ],
            'nama_instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Instansi harus diisi',
                ]
            ],
        ];

        $validasi->setRules($aturan);
        if ($validasi->withRequest($this->request)->run()) {
            $nama_lengkap = ucwords($this->request->getPost('nama_lengkap'));
            $nim_nis = $this->request->getPost('nim_nis');
            $username = $this->request->getPost('username');
            $gender = $this->request->getPost('gender');
            $no_hp = $this->request->getPost('no_hp');
            $email = $this->request->getPost('email');
            $instansi = $this->request->getPost('instansi');
            $nama_instansi = strtoupper($this->request->getPost('nama_instansi'));

            $data = [
                'nama_lengkap' => $nama_lengkap,
                'nim_nis' => $nim_nis,
                'username' => $username,
                'jenis_kelamin' => $gender,
                'email' => $email,
                'foto' => 'profile.png',
                'password' => '12345678',
                'no_hp' => $no_hp,
                'instansi_pendidikan' => $instansi,
                'nama_instansi' => $nama_instansi,
                'is_verifikasi' => 'yes'
            ];
            $user = new MemberModel();
            $user->save($data);

            $hasil['sukses'] = true;
            notif_swal('success', 'Berhasil Tambah User');
        } else {
            // $hasil['sukses'] = false;
            $hasil = [
                // 'sukses' => false,
                'error' => [
                    'nama_lengkap' => $validasi->getError('nama_lengkap'),
                    'nim_nis' => $validasi->getError('nim_nis'),
                    'username' => $validasi->getError('username'),
                    'gender' => $validasi->getError('gender'),
                    'no_hp' => $validasi->getError('no_hp'),
                    'email' => $validasi->getError('email'),
                    'instansi' => $validasi->getError('instansi'),
                    'nama_instansi' => $validasi->getError('nama_instansi'),
                ],
            ];
        }

        return json_encode($hasil);
    }

    public function editUserModal()
    {
        $id = $this->request->getPost('id');
        $dataNim_Nis = $this->request->getPost('dataNim_Nis');

        $validasi = \Config\Services::validation();
        $aturan = [
            'edit_nama_lengkap' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Lengkap harus diisi',
                ]
            ],
            'edit_nim_nis' => [
                'rules' => 'required|is_unique[member.nim_nis, member_id,' . $id . ']',
                'errors' => [
                    'required' => 'NIM/NIS harus diisi',
                    'is_unique' => 'NIM/NIS sudah terdaftar'
                ]
            ],
            'edit_username' => [
                'rules' => 'required|is_unique[member.username, member_id,' . $id . ']|regex_match[/^\S+$/]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'is_unique' => 'Username sudah terdaftar',
                    'regex_match' => 'Username tidak boleh menggunakan spasi'
                ]
            ],
            'edit_password' => [
                'rules' => 'required|regex_match[/^\S+$/]|min_length[5]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'regex_match' => 'Password tidak boleh menggunakan spasi',
                    'min_length' => 'Minimum panjang password adalah 5 karakter'
                ]
            ],
            'edit_gender' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis Kelamin harus diisi',
                ]
            ],
            'edit_no_hp' => [
                'rules' => 'required|is_unique[member.no_hp, member_id,' . $id . ']|regex_match[/^08\d{8,12}$/]',
                'errors' => [
                    'required' => 'Nomor Telepon harus diisi',
                    'is_unique' => 'Nomor Telepon sudah terdaftar',
                    'regex_match' => 'Nomor Telepon tidak valid'
                ]
            ],
            'edit_email' => [
                'rules' => 'required|is_unique[member.email, member_id,' . $id . ']|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'is_unique' => 'Email sudah terdaftar',
                    'valid_email' => 'Email tidak valid'
                ]
            ],
            'edit_instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Instansi Pendidikan harus diisi',
                ]
            ],
            'edit_nama_instansi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Instansi harus diisi',
                ]
            ],
        ];

        $validasi->setRules($aturan);
        if ($validasi->withRequest($this->request)->run()) {
            $nama_lengkap = ucwords($this->request->getPost('edit_nama_lengkap'));
            $nim_nis = $this->request->getPost('edit_nim_nis');
            $username = $this->request->getPost('edit_username');
            $password = $this->request->getPost('edit_password');
            $gender = $this->request->getPost('edit_gender');
            $no_hp = $this->request->getPost('edit_no_hp');
            $email = $this->request->getPost('edit_email');
            $instansi = $this->request->getPost('edit_instansi');
            $nama_instansi = strtoupper($this->request->getPost('edit_nama_instansi'));

            $data = [
                'member_id' => $id,
                'nama_lengkap' => $nama_lengkap,
                'nim_nis' => $nim_nis,
                'username' => $username,
                'password' => $password,
                'jenis_kelamin' => $gender,
                'email' => $email,
                'no_hp' => $no_hp,
                'instansi_pendidikan' => $instansi,
                'nama_instansi' => $nama_instansi,
                'foto_instansi' => null,
            ];
            $user = new MemberModel();
            $user->save($data);
            $absensi = new Absensi();
            $absensi->where('nim_nis', $dataNim_Nis)->set(['nim_nis' => $nim_nis])->update();

            $hasil_edit['sukses'] = true;
            notif_swal('success', 'Berhasil Edit User');

        } else {
            $hasil_edit = [
                'error' => [
                    'edit_nama_lengkap' => $validasi->getError('edit_nama_lengkap'),
                    'edit_nim_nis' => $validasi->getError('edit_nim_nis'),
                    'edit_username' => $validasi->getError('edit_username'),
                    'edit_password' => $validasi->getError('edit_password'),
                    'edit_gender' => $validasi->getError('edit_gender'),
                    'edit_no_hp' => $validasi->getError('edit_no_hp'),
                    'edit_email' => $validasi->getError('edit_email'),
                    'edit_instansi' => $validasi->getError('edit_instansi'),
                    'edit_nama_instansi' => $validasi->getError('edit_nama_instansi'),
                ],
            ];
        }

        return json_encode($hasil_edit);
    }

    public function editUser($id)
    {
        $user = new MemberModel();
        $data = $user->find($id);
        return json_encode($data);
    }

    public function hapus($id)
    {
        $user = new MemberModel();
        $dataUser = $user->find($id);
        $file = (FCPATH . 'uploadFoto/' . $dataUser['foto']);
        if (file_exists($file)) {
            if ($dataUser['foto'] != 'profile.png' && $dataUser['foto'] != '') {
                unlink($file);
            }
        }
        $user->delete($id);
        notif_swal('success', 'Terhapus');
        return redirect()->back();
    }

    public function hapus_absen($id)
    {
        $absensi = new Absensi();
        $dataAbsen = $absensi->find($id);
        $file = (FCPATH . 'uploadFoto/' . $dataAbsen['foto_absen']);
        if (file_exists($file)) {
            if ($dataAbsen['foto_absen'] != '') {
                unlink($file);
            }
        }
        $absensi->delete($id);
        notif_swal('success', 'Terhapus');

        return redirect()->back();
    }

    public function tambahAbsensi()
    {
        $validasi = \Config\Services::validation();
        $status = $this->request->getPost('status');
        $aturan = [
            'tanggal' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal absen harus diisi',
                ]
            ],
            'absen_nim_nis' => [
                'rules' => 'required|is_not_unique[member.nim_nis]',
                'errors' => [
                    'required' => 'NIM / NIS harus diisi',
                    'is_not_unique' => 'NIM / NIS tidak terdaftar',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status absen harus diisi',
                ]
            ],
            'checkin' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Waktu Checkin harus diisi',
                ]
            ],
        ];
        if ($status == 'Izin' or $status == 'Sakit') {
            $aturan = [
                'tanggal' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Tanggal absen harus diisi',
                    ]
                ],
                'absen_nim_nis' => [
                    'rules' => 'required|is_not_unique[member.nim_nis]',
                    'errors' => [
                        'required' => 'NIM / NIS harus diisi',
                        'is_not_unique' => 'NIM / NIS tidak terdaftar',
                    ]
                ],
                'status' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Status absen harus diisi',
                    ]
                ],
                'checkin' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Waktu Checkin harus diisi',
                    ]
                ],
                'keterangan' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Keterangan harus diisi',
                    ]
                ],
            ];
        }
        $validasi->setRules($aturan);
        if ($validasi->withRequest($this->request)->run()) {
            $hasil['sukses'] = true;
        } else {
            $hasil = [
                'error' => [
                    'tanggal' => $validasi->getError('tanggal'),
                    'absen_nama_lengkap' => $validasi->getError('absen_nama_lengkap'),
                    'absen_nim_nis' => $validasi->getError('absen_nim_nis'),
                    'absen_jenis_user' => $validasi->getError('absen_jenis_user'),
                    'status' => $validasi->getError('status'),
                    'checkin' => $validasi->getError('checkin'),
                    'keterangan' => $validasi->getError('keterangan'),
                    'foto' => $validasi->getError('foto'),
                ]
            ];
        }
        return json_encode($hasil);
    }

    public function tambahAbsensiProcess()
    {
        $rules = $this->validate([
            'foto' => [
                'rules' => 'is_image[foto]|ext_in[foto,jpg,png,jpeg]',
                'errors' => [
                    'is_image' => 'Foto tidak valid',
                    'ext_in' => 'Hanya .jpg, .jpeg, dan .png yang dapat diupload'
                ]
            ]
        ]);
        if (!$rules) {
            notif_swal('error', 'Foto tidak valid');
            return redirect()->back();
        }
        $tanggal = $this->request->getPost('tanggal');
        $nim_nis = $this->request->getPost('absen_nim_nis');
        $status = $this->request->getPost('status');
        $nim_nis = $this->request->getPost('absen_nim_nis');
        $keterangan = $this->request->getPost('keterangan');
        $checkin = $this->request->getVar('checkin');
        $foto = $this->request->getFile('foto');

        if ($foto->isValid()) {
            $namaFile = date("Y.m.d") . " - " . date("H.i.s") . ".jpeg";
            $foto->move(FCPATH . "uploadFoto", $namaFile);
        } else {
            $namaFile = '';
        }
        $user = new MemberModel();
        $infoUser = $user->where('nim_nis', $nim_nis)->first();
        $data = [
            'nama_lengkap' => $infoUser['nama_lengkap'],
            'nim_nis' => $nim_nis,
            'jenis_user' => $infoUser['jenis_user'],
            'status' => $status,
            'keterangan' => $keterangan,
            'waktu_absen' => $tanggal,
            'foto_profile' => $infoUser['foto'],
            'nama_instansi' => $infoUser['nama_instansi'],
            'instansi_pendidikan' => $infoUser['instansi_pendidikan'],
            'checkin_time' => $checkin,
            'foto_absen' => $namaFile
        ];
        $tambahAbsensi = new Absensi();
        $tambahAbsensi->save($data);

        notif_swal('success', 'Berhasil Tambah Absen');
        return redirect()->to('admin/data-absen');
    }

    public function checkout($id)
    {
        $absensi = new Absensi();
        $data = $absensi->find($id);

        return json_encode($data);
    }

    public function checkoutModal()
    {
        $validation = \Config\Services::validation();
        $aturan = [
            'checkout' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Waktu Checkout harus diisi!'
                ]
            ],
        ];
        $validation->setRules($aturan);
        if ($validation->withRequest($this->request)->run()) {
            $id = $this->request->getVar('id');
            $checkout = $this->request->getVar('checkout');

            if ($checkout > '17:00') {
                $checkout = 'Overtime : ' . $checkout;
            } else {
                $checkout;
            }
            $data = [
                'id' => $id,
                'checkout_time' => $checkout,
            ];
            $absensi = new Absensi();
            $absensi->save($data);

            $hasil['sukses'] = true;
            notif_swal('success', 'Berhasil Checkout');
        } else {
            $hasil = [
                'error' => [
                    'checkout' => $validation->getError('checkout'),
                ]
            ];
        }

        return json_encode($hasil);
    }

    public function instansi($nama)
    {
        $user = new MemberModel();
        $jumlahSiswa = $user->where('nama_instansi', $nama)->countAllResults();
        $dataUser = $user->where('nama_instansi', $nama)->first();

        $data = [
            'namaInstansi' => $dataUser['nama_instansi'],
            'jumlahSiswa' => $jumlahSiswa,
            'instansiPendidikan' => $dataUser['instansi_pendidikan'],
        ];

        return json_encode($data);
    }

    public function instansi_modal()
    {
        $logo = $this->request->getFile('foto_logo');
        $nama_instansi = $this->request->getVar('instansi_nama');
        $user = new MemberModel();
        $instansi = new Instansi();
        if ($logo->isValid()) {
            $dataInstansi = $instansi->where('nama_instansi', $nama_instansi)->first();
            if ($dataInstansi != null) {
                if ($dataInstansi['foto_instansi'] != '') {
                    $foto_lama = (FCPATH . 'uploadFoto/' . $dataInstansi['foto_instansi']);
                    if (file_exists($foto_lama)) {
                        unlink($foto_lama);
                    }
                }
            }

            $namaFile = date("Y.m.d") . " - " . date("H.i.s") . '.jpeg';
            $logo->move(FCPATH . 'uploadFoto/', $namaFile);

            $data = [
                'nama_instansi' => $nama_instansi,
                'foto_instansi' => $namaFile
            ];
            $dataInstansi = $instansi->where('nama_instansi', $nama_instansi)->first();
            if ($dataInstansi) {
                $instansi->where('nama_instansi', $nama_instansi)->set($data)->update();
            } else if (!$dataInstansi) {
                $instansi->insert($data);
            }
            notif_swal('success', 'Berhasil upload logo');
            return redirect()->back();
        } else {
            notif_swal('warning', 'Silahkan pilih file logo');
            return redirect()->back();
        }
    }
}