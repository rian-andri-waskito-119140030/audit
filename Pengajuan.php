<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan extends CI_Controller
{
    var $data_defol = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->library('form_validation');
        $this->load->library('template_web');
        $this->load->model('main_web');
        CekUserLog();

        $group = $this->uri->segment(2);
        $link = $this->uri->segment(3);
        $this->db->query("SET time_zone='+07:00'");

        if ($this->uri->segment(2) != 'reload_captcha' & $this->uri->segment(2) != 'cek_captcha') {
            $this->data_defol['detail_layanan'] = $this->main_web->getThisLay($link, $group, TargetLay());
            $this->data_defol['opsi_ambil_layanan'] = json_decode($this->data_defol['detail_layanan']['opsi_ambil']);
            $this->data_defol['opsi_ambil_master'] = array();
            if (isset($this->data_defol['opsi_ambil_layanan'])) {
                foreach ($this->data_defol['opsi_ambil_layanan'] as $key => $value) {
                    $dataOpsi = $this->db->get_where('opsi_pengambilan', ['active' => 1, 'id_opsi' => $value])->row_array();
                    if ($dataOpsi)
                        $this->data_defol['opsi_ambil_master'][] = $dataOpsi;
                }
            }
            // $this->data_defol['opsi_ambil_master'][] = $this->db->get_where('opsi_pengambilan', ['active' => 1])->row_array();
            $this->data_defol['cetak_mandiri'] = $this->data_defol['detail_layanan']['cetak_mandiri'];

            if (!$this->data_defol['detail_layanan']) {
                show_404($page = 'error404', $log_error = TRUE);
            }

            $this->data_defol['jam_ops'] = OpenPelayanan();
            if ($this->data_defol['jam_ops'] == 'close')
                $this->data_defol['data_jam_ops'] = $this->db->get('jam_operasional')->result_array();
            $this->data_defol['title1'] = $this->data_defol['detail_layanan']['nama_layanan'];
            $this->data_defol['title2'] = $this->main_web->getIdWeb()['nama_aplikasi'];
            cek_publish();
        }
        // echo $data
        // $correct_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    }




    public function index()
    {
        // $id_lay = $this->data_defol['detail_layanan']['id_lay'];
        // $group = $this->uri->segment(2);
        // $link_lay = $this->uri->segment(3);
        // $act = $this->uri->segment(4);
        // $data = $this->data_defol;

        // //Start Deteksi Layanan
        // if ($this->data_defol['detail_layanan']['link_group'] == $group) {
        //     if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_lahir') {
        //         $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
        //         $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //     } else 
        //     if ($this->data_defol['detail_layanan']['form_tambahan'] == 'perekaman') {
        //         $data['loket_rekam'] = $this->main_web->getLoketList();
        //     } else 
        //     if ($this->data_defol['detail_layanan']['form_tambahan'] == 'pindah_datang') {
        //         $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //         $data['prop'] =  $this->main_web->getProp();
        //         $data['alasan_pindah'] =  $this->db->get('alasan_pindah')->result_array();
        //     } else 
        //     if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kematian') {
        //         $data['sebab_mati'] = $this->db->get('sebab_mati')->result_array();
        //         $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //     } else 
        //     if ($this->data_defol['detail_layanan']['form_tambahan'] == 'kia') {
        //         $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
        //         $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //     }

        //     // Deteksi Request
        //     if ($act == 'add' &  $this->uri->segment(5) == '') {

        //         $this->form_validation->set_rules('captcha', 'Kode Capthca', 'trim|required|max_length[5]|min_length[5]|is_natural|callback_validate_captcha');
        //         $this->form_validation->set_rules('nik', 'Nik', 'trim|required|min_length[16]|max_length[16]|is_natural');
        //         $this->form_validation->set_rules('no_kk', 'Nomor KK', 'trim|required|min_length[16]|max_length[16]|is_natural');
        //         $this->form_validation->set_rules('alasan', 'Alasan', 'trim|required|min_length[9]|max_length[9]|is_natural');
        //         // $this->form_validation->set_rules('nama_lgkp', 'Nama Lengkap', 'trim|xss_clean|required|max_length[100]');
        //         $this->form_validation->set_rules('file_name_uploads[]', 'File Uploads', 'trim|xss_clean|required');
        //         $this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|required|valid_email|max_length[100]');
        //         $this->form_validation->set_rules('no_telp', 'Nomor Telfon', 'trim|xss_clean|required|max_length[16]|min_length[10]|is_natural');
        //         $this->form_validation->set_rules('kecamatan_val', 'Kecamatan', 'trim|xss_clean|required|max_length[6]|min_length[6]|is_natural');
        //         $this->form_validation->set_rules('desa_val', 'Desa Kelurahan', 'trim|xss_clean|required|max_length[10]|min_length[10]|is_natural');

        //         if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_lahir') {

        //             $this->form_validation->set_rules('no_kk_anak', 'Nomor KK Anak', 'trim|xss_clean|required|min_length[16]|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('jenis_lahir', 'Jenis Kelahiran', 'trim|xss_clean|required|max_length[5]|is_natural');
        //             $this->form_validation->set_rules('anak_ke', 'Anak Ke', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required|max_length[12]|min_length[9]');
        //             $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nama_anak', 'Nama Anak', 'trim|required|max_length[100]');
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'perekaman') {

        //             $this->form_validation->set_rules('id_loket', 'ID Loket', 'trim|required|is_natural');
        //             $this->form_validation->set_rules('id_tgl_rekam', 'ID Tgl Rekam', 'trim|required|is_natural');
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'pindah_datang') {

        //             $this->form_validation->set_rules('jenis_mutasi', 'Jenis Mutasi', 'trim|required|max_length[7]');


        //             if ($this->input->post('jenis_mutasi') == 'pindah') {

        //                 $this->form_validation->set_rules('prop_tujuan', 'Prop Tujuan', 'trim|required|is_natural|max_length[4]');
        //                 $this->form_validation->set_rules('kab_tujuan', 'Kabupaten Tujuan', 'trim|required|is_natural|max_length[4]');
        //                 $this->form_validation->set_rules('kec_tujuan', 'Kecamtan Tujuan', 'trim|required|is_natural|max_length[6]');
        //                 $this->form_validation->set_rules('desa_tujuan', 'Desa Tujuan', 'trim|required|is_natural|max_length[10]');
        //                 $this->form_validation->set_rules('alasan_pindah', 'Alasan Pindah', 'trim|required|is_natural|max_length[4]');
        //                 $this->form_validation->set_rules('alamat_pindah', 'Alamat Tujuan', 'trim|required|max_length[500]');
        //                 $this->form_validation->set_rules('no_rt_pindah', 'Nomor Rt', 'trim|required|max_length[3]|is_natural');
        //                 $this->form_validation->set_rules('no_rw_pindah', 'Nomor RW', 'trim|required|max_length[3]|is_natural');
        //                 $this->form_validation->set_rules('jumlah_pengikut', 'Jml Pengikut', 'trim|required|max_length[3]|is_natural');
        //             } else if ($this->input->post('jenis_mutasi') == 'datang') {

        //                 $this->form_validation->set_rules('no_skpwni', 'Nomor SKPWNI', 'trim|required|max_length[50]');
        //                 $this->form_validation->set_rules('alamat_datang', 'Alamat Datang', 'trim|required|max_length[500]');
        //                 $this->form_validation->set_rules('no_rt_datang', 'Alamat Datang', 'trim|required|max_length[3]|is_natural');
        //                 $this->form_validation->set_rules('no_rw_datang', 'Alamat Datang', 'trim|required|max_length[3]|is_natural');
        //             }
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kematian') {

        //             $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[3]|is_natural');
        //             $this->form_validation->set_rules('sebab_kematian', 'Sebab Kematian', 'trim|required|max_length[3]|is_natural');
        //             $this->form_validation->set_rules('tempat_lahir_jenazah', 'Tempat Lahir Jenazah', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tempat_kematian', 'Tempat Kematian', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir_jenazah', 'Tgl Lahir Jenazah', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('tgl_mati_jenazah', 'Tgl Kematian', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('anak_ke', 'Anak Ke Jenazah', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('nama_ayah', 'Nama Ayah Jenazah', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nama_ibu', 'Nama Ibu Jenazah', 'trim|required|max_length[100]');
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'kia') {

        //             $this->form_validation->set_rules('no_akta_lhr', 'Nomor Akta Anak', 'trim|required|max_length[50]');
        //             $this->form_validation->set_rules('no_kk_anak', 'Nomor KK Anak', 'trim|required|min_length[16]|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nik_anak', 'Nik Anak', 'trim|required|min_length[16]|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('anak_ke', 'Anak Ke', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nama_anak', 'Nama Anak', 'trim|required|max_length[100]');
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_cerai') {

        //             $this->form_validation->set_rules('pengaju_cerai', 'pengaju cerai', 'trim|required|max_length[2]');
        //             $this->form_validation->set_rules('nomor_putusan', 'nomor putusan', 'trim|required|max_length[50]');
        //             $this->form_validation->set_rules('tgl_cerai', 'tgl_cerai', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('sebab_cerai', 'sebab_cerai', 'trim|required|max_length[2]');
        //             $this->form_validation->set_rules('no_akta_kawin_cerai', 'no_akta_kawin_cerai', 'trim|required|max_length[50]');
        //             $this->form_validation->set_rules('tgl_kawin_cerai', 'tgl_kawin_cerai', 'trim|required|max_length[12]');
        //             //
        //             $this->form_validation->set_rules('nik_suami', 'nik_suami', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('no_kk_cerai', 'no_kk_cerai', 'required|trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_suami', 'nama_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tempat_lahir_suami', 'tempat_lahir_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir_suami', 'tgl_lahir_suami', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('nik_ayah_suami', 'nik_ayah_suami', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ayah_suami', 'nama_ayah_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nik_ibu_suami', 'nik_ibu_suami', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu_suami', 'nama_ibu_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('cerai_suami_ke', 'cerai_suami_ke', 'trim|required|max_length[2]|is_natural');
        //             //
        //             $this->form_validation->set_rules('nik_istri', 'nik_istri', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_istri', 'nama_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tempat_lahir_istri', 'tempat_lahir_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir_istri', 'tgl_lahir_istri', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('nik_ayah_istri', 'nik_ayah_istri', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ayah_istri', 'nama_ayah_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nik_ibu_istri', 'nik_ibu_istri', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu_istri', 'nama_ibu_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('cerai_istri_ke', 'cerai_istri_ke', 'trim|required|max_length[2]|is_natural');
        //             //
        //         } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kawin') {

        //             $this->form_validation->set_rules('tgl_kawin', 'tgl_kawin', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('tempat_kawin', 'no_akta_kawin_cerai', 'trim|required|max_length[50]');
        //             $this->form_validation->set_rules('nama_pemuka', 'nama_pemuka', 'trim|required|max_length[50]');

        //             //
        //             $this->form_validation->set_rules('nik_suami', 'nik_suami', 'required|trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('no_kk_suami', 'no_kk_suami', 'required|trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_suami', 'nama_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tempat_lahir_suami', 'tempat_lahir_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir_suami', 'tgl_lahir_suami', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('nik_ayah_suami', 'nik_ayah_suami', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ayah_suami', 'nama_ayah_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nik_ibu_suami', 'nik_ibu_suami', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu_suami', 'nama_ibu_suami', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('kawin_suami_ke', 'kawin_suami_ke', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('anak_ke_suami', 'anak_ke_suami', 'trim|required|max_length[2]|is_natural');

        //             //
        //             $this->form_validation->set_rules('nik_istri', 'nik_istri', 'required|trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('no_kk_istri', 'no_kk_istri', 'required|trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_istri', 'nama_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tempat_lahir_istri', 'tempat_lahir_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('tgl_lahir_istri', 'tgl_lahir_istri', 'trim|required|max_length[12]');
        //             $this->form_validation->set_rules('nik_ayah_istri', 'nik_ayah_istri', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ayah_istri', 'nama_ayah_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('nik_ibu_istri', 'nik_ibu_istri', 'trim|max_length[16]|is_natural');
        //             $this->form_validation->set_rules('nama_ibu_istri', 'nama_ibu_istri', 'trim|required|max_length[100]');
        //             $this->form_validation->set_rules('kawin_istri_ke', 'kawin_istri_ke', 'trim|required|max_length[2]|is_natural');
        //             $this->form_validation->set_rules('anak_ke_istri', 'anak_ke_istri', 'trim|required|max_length[2]|is_natural');
        //             //

        //         }

        //         //
        //         if ($this->form_validation->run() != false) {
        //             $no_kk = $this->input->post('no_kk');
        //             $nik = $this->input->post('nik');
        //             $alasan = $this->input->post('alasan');
        //             $post = array();
        //             $post = $this->input->post();
        //             $cekNIK = $this->_ceknik($nik, $no_kk, $alasan);


        //             if ($cekNIK['status'] == 5) {

        //                 $post['data_cek'] = [$cekNIK];
        //                 $cek = $this->main_web->InsTbPemohon($post);
        //                 if ($cek) {
        //                     $this->_Swall('success', 'Sukses...!!', 'Pengajuan Berhasil, Simpan Nomor Registrasi : ' . $this->session->userdata('noRegSukses') . ' atau Silahkan Cek Inbox Email', 'Beranda', 'Cek Data', false, 'window.location.href ="' . base_url('main/cekdata') . '"', 'window.location.href ="' . base_url('main') . '"');
        //                 } else {
        //                     $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL', 'Beranda', 'Beranda', false, 'window.location.href ="' . base_url('main') . '"', 'window.location.href ="' . base_url('main') . '"');
        //                 }
        //             } else {
        //                 $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL Data Sudah Pernah Ada', 'Beranda', 'Beranda', false, 'window.location.href ="' . base_url('main') . '"', 'window.location.href ="' . base_url('main') . '"');
        //             }
        //             redirect('pengajuan/' . $group . '/' . $link_lay . '/' . $act, 'refresh');
        //         } else {

        //             // Jika Request Tambah
        //             if (!$this->data_defol['detail_layanan']) {
        //                 show_404($page = 'error404', $log_error = TRUE);
        //             }
        //             $data['alasan'] = $this->main_web->getAlasanLay($id_lay);
        //             $data['url_cek_nik'] = md5('ceknik' . session_id());
        //             $data['url_getsyarat'] = md5('getsyarat' . session_id());
        //             $data['url_get_kab'] = md5('getkab' . session_id());
        //             $data['url_get_tglrekam'] = md5('gettanggalrekam' . session_id());
        //             $data['url_get_kec'] = md5('getkec' . session_id());
        //             $data['url_get_desa'] = md5('getdesa' . session_id());
        //             $data['url_post_gambar'] = md5('geturlpostgambar' . session_id() . base_url());

        //             $data['image'] = $this->get_captcha();
        //             //load View
        //             $this->template_web->load('web/page/main_form', $data);
        //         }


        //         // if ($this->input->post('nik')  != '') {
        //         //     $cek = $this->main_web->InsTbPemohon($this->input->post());
        //         //     if ($cek) {
        //         //         $this->_Swall('success', 'Sukses...!!', 'Pengajuan Berhasil', 'Beranda', 'Cek Data', true, 'window.location.href ="' . base_url('main/cekdata') . '"', 'window.location.href ="' . base_url('main') . '"');
        //         //     } else {
        //         //         $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL', 'Beranda', 'Cek Data', false, 'window.location.href ="' . base_url('main/cekdata') . '"', 'window.location.href ="' . base_url('main') . '"');
        //         //     }
        //         //     redirect('pengajuan/' . $group . '/' . $link_lay . '/' . $act, 'refresh');
        //         // }

        //     } else if ($act == 'edit') {

        //         if (!$this->data_defol['detail_layanan']) {
        //             show_404($page = 'error404', $log_error = TRUE);
        //         }
        //         $post = $this->input->post();
        //         if ($this->input->post('no_reg')  != '') {

        //             // print_r($_POST['file_name_uploads']);
        //             // $no_reg = $this->input->post('no_reg');
        //             // $nama_lgkp = $this->input->post('nama_lgkp');
        //             // $email = $this->input->post('email');
        //             // $no_telp = $this->input->post('no_telp');
        //             // $cttn_pemohon = $this->input->post('no_telp');
        //             // $form_tambahan = $this->input->post('form_tambahan');

        //             // $dataUpdate = [

        //             //     'nama_lgkp' => $nama_lgkp,
        //             //     'email' => $email,
        //             //     'no_telp' => $no_telp,
        //             //     'cttn_pemohon' => $cttn_pemohon,
        //             //     'cttn_petugas' => '',
        //             //     'proses' => '5',
        //             // ];
        //             // $this->db->set('tgl_pengajuan', 'NOW()', FALSE);
        //             // $this->db->update('web_pemohon', $dataUpdate, ['no_reg' => $no_reg]);

        //             // $this->db->set('use', 'Y');
        //             // $this->db->where('no_reg', $no_reg);
        //             // $this->db->update('web_token_edit');

        //             // // update token jadi Y
        //             // // update table_pemohon jadi 5
        //             // // update tanggal pengajuan jadi now
        //             // // jika ada form_tambahan maka edit jg form tambahannya
        //             print_r($post);
        //         } else {
        //             //jika Edit Pada Form
        //             $no_reg = $this->session->userdata('noregEdit');
        //             // print_r($this->session->userdata());
        //             $cek = $this->db->get_where('web_pemohon', ['no_reg' => $no_reg, 'proses' => '4'])->num_rows();

        //             if ($cek == 0) {
        //                 show_404($page = 'error404', $log_error = TRUE);
        //             }
        //             // echo $no_reg;

        //             $data['data_edit'] = $this->db->get_where('web_pemohon', ['no_reg' => $no_reg, 'proses' => '4'])->row_array();
        //             // var_dump($data['data_form_']);


        //             $dataform = $data['data_edit']['form_tambahan'];
        //             echo $dataform;
        //             $data['data_form_'] = array();
        //             if ($dataform != '') {
        //                 $data['data_form_'] = $this->db->get_where($data['data_edit']['form_tambahan'], ['no_reg' => $no_reg])->row_array();
        //                 if ($data['data_edit']['form_tambahan'] == 'akta_lahir') {
        //                     $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
        //                     $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //                 } else if ($data['data_edit']['form_tambahan'] == 'perekaman') {
        //                     //$data['data_edit'] = $this->db->get_where('perekaman', ['no_reg' => $no_reg])->row_array();
        //                     //$data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
        //                 }
        //             }

        //             // var_dump($data['data_form_']);
        //             $data['alasan'] = $this->main_web->getAlasanLayEdit($data['data_edit']['id_lay'], $data['data_edit']['id_alasan']);
        //             $data['Get_filename'] = $this->db->get_where('web_upload_syarat', ['no_reg' => $no_reg, 'id_lay' => $data['data_edit']['id_lay'], 'id_alasan' => $data['data_edit']['id_alasan']])->result_array();


        //             $this->db->select('web_detail_syarat.nama_syarat, web_detail_syarat.deskripsi, web_detail_syarat.required, web_detail_syarat.id_syarat, web_detail_syarat.form, web_detail_syarat.file_unduh');
        //             $this->db->from('web_detail_syarat');
        //             $this->db->join('web_data_syarat', 'web_detail_syarat.id_syarat = web_data_syarat.id_syarat');
        //             $this->db->where('web_data_syarat.id_lay', $data['data_edit']['id_lay']);
        //             $this->db->where('web_data_syarat.id_alasan', $data['data_edit']['id_alasan']);
        //             $data['syarat_upload'] = $this->db->get()->result_array();


        //             for ($i = 0; $i < count($data['syarat_upload']); $i++) {
        //                 foreach ($data['Get_filename'] as $keyFilename) {
        //                     if ($data['syarat_upload'][$i]['id_syarat'] == $keyFilename['id_syarat']) {
        //                         $data['syarat_upload'][$i]['file'] = $keyFilename['file'];
        //                     }
        //                 }
        //             }


        //             $this->session->set_userdata('TokenSess', 'Permitted');
        //             $this->session->set_userdata('MySession', session_id());
        //             $data['alasan'] = $this->main_web->getAlasanLay($id_lay);
        //             $data['url_cek_nik'] = md5('ceknik' . session_id());
        //             $data['url_getsyarat'] = md5('getsyarat' . session_id());
        //             $data['url_get_kab'] = md5('getkab' . session_id());
        //             $data['url_get_tglrekam'] = md5('gettanggalrekam' . session_id());
        //             $data['url_get_kec'] = md5('getkec' . session_id());
        //             $data['url_get_desa'] = md5('getdesa' . session_id());
        //             $data['url_post_gambar'] = md5('geturlpostgambar' . session_id() . base_url());

        //             $data['image'] = $this->get_captcha();
        //             $this->template_web->load('web/page/edit_form', $data);
        //             // $this->session->set_userdata('noregEdit', '');
        //         }
        //     } else {
        //         $this->session->set_userdata('TokenSess', '');
        //         show_404($page = 'error404', $log_error = TRUE);
        //     }
        // } else {
        //     show_404($page = 'error404', $log_error = TRUE);
        // }
    }

    public function add()
    {
        // die;
        // $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL', 'Beranda', 'Beranda', false, 'window.location.href ="' . base_url('main') . '"', 'window.location.href ="' . base_url('main') . '"');

        $id_lay = $this->data_defol['detail_layanan']['id_lay'];
        $group = $this->uri->segment(2);
        $link_lay = $this->uri->segment(3);
        $act = $this->uri->segment(4);
        $data = $this->data_defol;
        // $data['cetak_mandiri'] = '1';

        //Start Deteksi Layanan
        if ($this->data_defol['detail_layanan']['link_group'] == $group) {
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_lahir') {
                $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'perekaman') {
                $data['loket_rekam'] = $this->main_web->getLoketList();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'pindah_datang') {
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
                $data['prop'] =  $this->main_web->getProp();
                $data['alasan_pindah'] =  $this->db->get('alasan_pindah')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kematian') {
                $data['sebab_mati'] = $this->db->get('sebab_mati')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'kia') {
                $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'yanduk') {
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            }
        }

        $this->form_validation->set_rules('captcha', 'Kode Capthca', 'trim|required|max_length[5]|min_length[5]|is_natural|callback_validate_captcha');
        $this->form_validation->set_rules('nik', 'Nik', 'trim|required|min_length[16]|max_length[16]|is_natural');
        $this->form_validation->set_rules('no_kk', 'Nomor KK', 'trim|required|min_length[16]|max_length[16]|is_natural');
        $this->form_validation->set_rules('alasan', 'Alasan', 'trim|required|min_length[9]|max_length[9]|is_natural');
        $this->form_validation->set_rules('nama_lgkp', 'Nama Lengkap', 'trim|xss_clean|required|max_length[100]');
        $this->form_validation->set_rules('file_name_uploads[]', 'File Uploads', 'trim|xss_clean|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|required|valid_email|max_length[100]');
        $this->form_validation->set_rules('no_telp', 'Nomor Telfon', 'trim|xss_clean|required|max_length[16]|min_length[10]|is_natural');
        $this->form_validation->set_rules('kecamatan_val', 'Kecamatan', 'trim|xss_clean|required|max_length[6]|min_length[6]|is_natural');
        $this->form_validation->set_rules('desa_val', 'Desa Kelurahan', 'trim|xss_clean|required|max_length[10]|min_length[10]|is_natural');

        if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_lahir') {

            $this->form_validation->set_rules('no_kk_anak', 'Nomor KK Anak', 'trim|xss_clean|required|min_length[16]|max_length[16]|is_natural');
            $this->form_validation->set_rules('jenis_lahir', 'Jenis Kelahiran', 'trim|xss_clean|required|max_length[5]|is_natural');
            $this->form_validation->set_rules('anak_ke', 'Anak Ke', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required|max_length[12]|min_length[9]');
            $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nama_anak', 'Nama Anak', 'trim|required|max_length[100]');
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'perekaman') {

            $this->form_validation->set_rules('id_loket', 'ID Loket', 'trim|required|is_natural');
            $this->form_validation->set_rules('id_tgl_rekam', 'ID Tgl Rekam', 'trim|required|is_natural');
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'pindah_datang') {

            $this->form_validation->set_rules('jenis_mutasi', 'Jenis Mutasi', 'trim|required|max_length[7]');

            if ($this->input->post('jenis_mutasi') == 'pindah') {
                $this->form_validation->set_rules('prop_tujuan', 'Prop Tujuan', 'trim|required|is_natural|max_length[4]');
                $this->form_validation->set_rules('kab_tujuan', 'Kabupaten Tujuan', 'trim|required|is_natural|max_length[4]');
                $this->form_validation->set_rules('kec_tujuan', 'Kecamtan Tujuan', 'trim|required|is_natural|max_length[6]');
                $this->form_validation->set_rules('desa_tujuan', 'Desa Tujuan', 'trim|required|is_natural|max_length[10]');
                $this->form_validation->set_rules('alasan_pindah', 'Alasan Pindah', 'trim|required|is_natural|max_length[4]');
                $this->form_validation->set_rules('alamat_pindah', 'Alamat Tujuan', 'trim|required|max_length[500]');
                $this->form_validation->set_rules('no_rt_pindah', 'Nomor Rt', 'trim|required|max_length[3]|is_natural');
                $this->form_validation->set_rules('no_rw_pindah', 'Nomor RW', 'trim|required|max_length[3]|is_natural');
                $this->form_validation->set_rules('jumlah_pengikut', 'Jml Pengikut', 'trim|required|max_length[3]|is_natural');
            } else if ($this->input->post('jenis_mutasi') == 'datang') {

                $this->form_validation->set_rules('no_skpwni', 'Nomor SKPWNI', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('alamat_datang', 'Alamat Datang', 'trim|required|max_length[500]');
                $this->form_validation->set_rules('no_rt_datang', 'Alamat Datang', 'trim|required|max_length[3]|is_natural');
                $this->form_validation->set_rules('no_rw_datang', 'Alamat Datang', 'trim|required|max_length[3]|is_natural');
            }
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kematian') {

            $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[3]|is_natural');
            $this->form_validation->set_rules('sebab_kematian', 'Sebab Kematian', 'trim|required|max_length[3]|is_natural');
            $this->form_validation->set_rules('tempat_lahir_jenazah', 'Tempat Lahir Jenazah', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tempat_kematian', 'Tempat Kematian', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir_jenazah', 'Tgl Lahir Jenazah', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('tgl_mati_jenazah', 'Tgl Kematian', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('anak_ke', 'Anak Ke Jenazah', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('nama_ayah', 'Nama Ayah Jenazah', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nama_ibu', 'Nama Ibu Jenazah', 'trim|required|max_length[100]');
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'kia') {

            $this->form_validation->set_rules('no_akta_lhr', 'Nomor Akta Anak', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_kk_anak', 'Nomor KK Anak', 'trim|required|min_length[16]|max_length[16]|is_natural');
            $this->form_validation->set_rules('nik_anak', 'Nik Anak', 'trim|required|min_length[16]|max_length[16]|is_natural');
            $this->form_validation->set_rules('anak_ke', 'Anak Ke', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('hub_pemohon', 'Hubungan Pemohon', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nama_anak', 'Nama Anak', 'trim|required|max_length[100]');
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_cerai') {

            $this->form_validation->set_rules('pengaju_cerai', 'pengaju cerai', 'trim|required|max_length[2]');
            $this->form_validation->set_rules('nomor_putusan', 'nomor putusan', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('tgl_cerai', 'tgl_cerai', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('sebab_cerai', 'sebab_cerai', 'trim|required|max_length[2]');
            $this->form_validation->set_rules('no_akta_kawin_cerai', 'no_akta_kawin_cerai', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('tgl_kawin_cerai', 'tgl_kawin_cerai', 'trim|required|max_length[12]');
            //
            $this->form_validation->set_rules('nik_suami', 'nik_suami', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('no_kk_cerai', 'no_kk_cerai', 'required|trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_suami', 'nama_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tempat_lahir_suami', 'tempat_lahir_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir_suami', 'tgl_lahir_suami', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('nik_ayah_suami', 'nik_ayah_suami', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ayah_suami', 'nama_ayah_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nik_ibu_suami', 'nik_ibu_suami', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ibu_suami', 'nama_ibu_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('cerai_suami_ke', 'cerai_suami_ke', 'trim|required|max_length[2]|is_natural');
            //
            $this->form_validation->set_rules('nik_istri', 'nik_istri', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_istri', 'nama_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tempat_lahir_istri', 'tempat_lahir_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir_istri', 'tgl_lahir_istri', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('nik_ayah_istri', 'nik_ayah_istri', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ayah_istri', 'nama_ayah_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nik_ibu_istri', 'nik_ibu_istri', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ibu_istri', 'nama_ibu_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('cerai_istri_ke', 'cerai_istri_ke', 'trim|required|max_length[2]|is_natural');
            //
        } else if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kawin') {

            $this->form_validation->set_rules('tgl_kawin', 'tgl_kawin', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('tempat_kawin', 'no_akta_kawin_cerai', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('nama_pemuka', 'nama_pemuka', 'trim|required|max_length[50]');

            //
            $this->form_validation->set_rules('nik_suami', 'nik_suami', 'required|trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('no_kk_suami', 'no_kk_suami', 'required|trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_suami', 'nama_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tempat_lahir_suami', 'tempat_lahir_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir_suami', 'tgl_lahir_suami', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('nik_ayah_suami', 'nik_ayah_suami', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ayah_suami', 'nama_ayah_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nik_ibu_suami', 'nik_ibu_suami', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ibu_suami', 'nama_ibu_suami', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('kawin_suami_ke', 'kawin_suami_ke', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('anak_ke_suami', 'anak_ke_suami', 'trim|required|max_length[2]|is_natural');

            //
            $this->form_validation->set_rules('nik_istri', 'nik_istri', 'required|trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('no_kk_istri', 'no_kk_istri', 'required|trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_istri', 'nama_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tempat_lahir_istri', 'tempat_lahir_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('tgl_lahir_istri', 'tgl_lahir_istri', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('nik_ayah_istri', 'nik_ayah_istri', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ayah_istri', 'nama_ayah_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('nik_ibu_istri', 'nik_ibu_istri', 'trim|max_length[16]|is_natural');
            $this->form_validation->set_rules('nama_ibu_istri', 'nama_ibu_istri', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('kawin_istri_ke', 'kawin_istri_ke', 'trim|required|max_length[2]|is_natural');
            $this->form_validation->set_rules('anak_ke_istri', 'anak_ke_istri', 'trim|required|max_length[2]|is_natural');
            //
        }
        //
        if ($this->form_validation->run() != false && $data['jam_ops'] == 'open') {
            $no_kk = $this->input->post('no_kk');
            $nik = $this->input->post('nik');
            $alasan = $this->input->post('alasan');

            $cekNIK = $this->_ceknik($nik, $no_kk, $alasan);

            if ($cekNIK['status'] == 5) {
                $cek = $this->main_web->InsTbPemohon($this->input->post(), $cekNIK);

                if ($cek) {
                    $this->_Swall('success', 'Sukses...!!', 'Pengajuan Berhasil, Simpan Nomor Registrasi : ' . $this->session->userdata('noRegSukses') . ' atau Silahkan Cek Inbox Email', 'Beranda', 'Cek Data', false, 'window.location.href ="' . base_url('main/cekdata') . '"', 'window.location.href ="' . base_url('main') . '"');
                } else {
                    $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL', 'Beranda', 'Beranda', false, 'window.location.href ="' . base_url('main') . '"', 'window.location.href ="' . base_url('main') . '"');
                }
            } else {
                $this->_Swall('error', 'Gagal...!!', 'Pengajuan GAGAL Data Sudah Pernah Ada', 'Beranda', 'Beranda', false, 'window.location.href ="' . base_url('main') . '"', 'window.location.href ="' . base_url('main') . '"');
            }

            redirect('pengajuan/' . $group . '/' . $link_lay . '/' . $act, 'refresh');
        } else {

            if (!$this->data_defol['detail_layanan']) {
                show_404($page = 'error404', $log_error = TRUE);
            }
            $data['alasan'] = $this->main_web->getAlasanLay($id_lay);
            $data['url_cek_nik'] = md5('ceknik' . session_id());
            $data['url_getsyarat'] = md5('getsyarat' . session_id());
            $data['url_get_kab'] = md5('getkab' . session_id());
            $data['url_get_tglrekam'] = md5('gettanggalrekam' . session_id());
            $data['url_get_kec'] = md5('getkec' . session_id());
            $data['url_get_desa'] = md5('getdesa' . session_id());
            $data['url_cek_tgl'] = md5('gettempatlahir' . session_id());
            $data['url_jasa_cetak'] = md5('getpagejasaantar' . session_id());
            $data['url_post_gambar'] = md5('geturlpostgambar' . session_id() . base_url());
            $data['url_post_crop'] = md5('postdataimage' . session_id() . base_url());
            $data['image'] = $this->get_captcha();


            //load View
            $this->template_web->load('web/page/main_form', $data);
        }
    }

    public function edit()
    {
        $id_lay = $this->data_defol['detail_layanan']['id_lay'];
        $group = $this->uri->segment(2);
        $link_lay = $this->uri->segment(3);
        $act = $this->uri->segment(4);
        $data = $this->data_defol;

        //Start Deteksi Layanan
        if ($this->data_defol['detail_layanan']['link_group'] == $group) {
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_lahir') {
                $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'perekaman') {
                $data['loket_rekam'] = $this->main_web->getLoketList();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'pindah_datang') {
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
                $data['prop'] =  $this->main_web->getProp();
                $data['alasan_pindah'] =  $this->db->get('alasan_pindah')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'akta_kematian') {
                $data['sebab_mati'] = $this->db->get('sebab_mati')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            } else 
            if ($this->data_defol['detail_layanan']['form_tambahan'] == 'kia') {
                $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
                $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
            }
        }


        $post = $this->input->post();
        if ($this->input->post('no_reg')  != '') {

            // print_r($_POST['file_name_uploads']);
            // $no_reg = $this->input->post('no_reg');
            // $nama_lgkp = $this->input->post('nama_lgkp');
            // $email = $this->input->post('email');
            // $no_telp = $this->input->post('no_telp');
            // $cttn_pemohon = $this->input->post('no_telp');
            // $form_tambahan = $this->input->post('form_tambahan');

            // $dataUpdate = [

            //     'nama_lgkp' => $nama_lgkp,
            //     'email' => $email,
            //     'no_telp' => $no_telp,
            //     'cttn_pemohon' => $cttn_pemohon,
            //     'cttn_petugas' => '',
            //     'proses' => '5',
            // ];
            // $this->db->set('tgl_pengajuan', 'NOW()', FALSE);
            // $this->db->update('web_pemohon', $dataUpdate, ['no_reg' => $no_reg]);

            // $this->db->set('use', 'Y');
            // $this->db->where('no_reg', $no_reg);
            // $this->db->update('web_token_edit');

            // // update token jadi Y
            // // update table_pemohon jadi 5
            // // update tanggal pengajuan jadi now
            // // jika ada form_tambahan maka edit jg form tambahannya
            print_r($post);
        } else {
            //jika Edit Pada Form
            $no_reg = $this->session->userdata('noregEdit');
            // echo $no_reg;
            if (!$no_reg)
                show_404($page = 'error404', $log_error = TRUE);

            // print_r($this->session->userdata());
            $cek = $this->db->get_where('web_pemohon', ['no_reg' => $no_reg, 'proses' => '4'])->num_rows();

            if ($cek == 0) {
                show_404($page = 'error404', $log_error = TRUE);
            }
            // echo $no_reg;

            $data['data_edit'] = $this->db->get_where('web_pemohon', ['no_reg' => $no_reg, 'proses' => '4'])->row_array();
            // var_dump($data['data_form_']);


            $dataform = $data['data_edit']['form_tambahan'];
            // echo $dataform;
            $data['data_form_'] = array();
            if ($dataform != '') {
                $data['data_form_'] = $this->db->get_where($data['data_edit']['form_tambahan'], ['no_reg' => $no_reg])->row_array();
                if ($data['data_edit']['form_tambahan'] == 'akta_lahir') {
                    $data['jenis_lahir'] = $this->db->get('web_jenis_lahir')->result_array();
                    $data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
                } else if ($data['data_edit']['form_tambahan'] == 'perekaman') {
                    //$data['data_edit'] = $this->db->get_where('perekaman', ['no_reg' => $no_reg])->row_array();
                    //$data['hub_pemohon'] = $this->db->get('web_hub_pemohon')->result_array();
                }
            }

            // var_dump($data['data_form_']);
            $data['alasan'] = $this->main_web->getAlasanLayEdit($data['data_edit']['id_lay'], $data['data_edit']['id_alasan']);
            $data['Get_filename'] = $this->db->get_where('web_upload_syarat', ['no_reg' => $no_reg, 'id_lay' => $data['data_edit']['id_lay'], 'id_alasan' => $data['data_edit']['id_alasan']])->result_array();


            $this->db->select('web_detail_syarat.nama_syarat, web_detail_syarat.deskripsi, web_detail_syarat.required, web_detail_syarat.id_syarat, web_detail_syarat.form, web_detail_syarat.file_unduh');
            $this->db->from('web_detail_syarat');
            $this->db->join('web_data_syarat', 'web_detail_syarat.id_syarat = web_data_syarat.id_syarat');
            $this->db->where('web_data_syarat.id_lay', $data['data_edit']['id_lay']);
            $this->db->where('web_data_syarat.id_alasan', $data['data_edit']['id_alasan']);
            $data['syarat_upload'] = $this->db->get()->result_array();


            for ($i = 0; $i < count($data['syarat_upload']); $i++) {
                foreach ($data['Get_filename'] as $keyFilename) {
                    if ($data['syarat_upload'][$i]['id_syarat'] == $keyFilename['id_syarat']) {
                        $data['syarat_upload'][$i]['file'] = $keyFilename['file'];
                    }
                }
            }


            $this->session->set_userdata('TokenSess', 'Permitted');
            $this->session->set_userdata('MySession', session_id());
            $data['alasan'] = $this->main_web->getAlasanLay($id_lay);
            $data['url_cek_nik'] = md5('ceknik' . session_id());
            $data['url_getsyarat'] = md5('getsyarat' . session_id());
            $data['url_get_kab'] = md5('getkab' . session_id());
            $data['url_get_tglrekam'] = md5('gettanggalrekam' . session_id());
            $data['url_get_kec'] = md5('getkec' . session_id());
            $data['url_get_desa'] = md5('getdesa' . session_id());
            $data['url_post_gambar'] = md5('geturlpostgambar' . session_id() . base_url());
            $data['url_cek_tgl'] = md5('gettempatlahir' . session_id());
            $data['image'] = $this->get_captcha();
            $this->template_web->load('web/page/edit_form', $data);
            // $this->session->set_userdata('noregEdit', '');
        }
        // if (!$this->data_defol['detail_layanan']) {
        //     show_404($page = 'error404', $log_error = TRUE);
        // }
        // $id_lay = $this->data_defol['detail_layanan']['id_lay'];
        // echo $id_lay;
    }



    private function _ceknik($nik = null, $no_kk = null, $alasan = null)
    {
        $this->load->model('main_web');

        $id_alasan = substr($alasan, -6);
        $id_lay = substr($alasan, 0, 3);

        if ($nik != '' or $no_kk != '' or $id_alasan != '') {

            $this->db->select('*');
            $this->db->from('web_pemohon');
            $this->db->where('id_lay', $id_lay);
            $this->db->where('nik', $nik);
            $this->db->where('no_kk', $no_kk);
            $this->db->where('id_alasan', $id_alasan);
            $this->db->where_not_in('proses', ['3', '7', '8', '10', '11', '12', '13']);
            $cekNik = $this->db->get()->num_rows();

            if ($cekNik > 0) {
                $data = [
                    'status' => 1
                ];
            } else {

                if ($alasan != '' and $alasan == '104104102') {
                    $data = [
                        'status' => 5
                    ];
                } else {
                    $api_use = $this->main_web->getIdWeb()['api'];

                    if ($api_use == 'Y') {

                        $this->load->model('cek_api');
                        $getTarget = $this->db->get_where('web_layanan', ['id_lay' => $id_lay])->row_array()['target'];
                        $dataget = $this->cek_api->GetResponse($nik, $no_kk, $getTarget);
                        if ($dataget['status'] != true) {
                            $data['status'] = 6;
                        } else if ($dataget['status'] === true) {
                            $data = $dataget;
                            $data['status'] = 5;
                        }
                        // $data['respon'] = $dataget['feedback'];
                    } else if ($api_use == 'N') {
                        $data = [
                            'status' => 5
                        ];
                    }
                }
            }
        } else {
            $data = [
                'status' => 6
            ];
        }
        return $data;
    }

    public function get_captcha()
    {
        $this->load->helper('captcha');

        $dircaptcha = "./captcha/";
        foreach (glob($dircaptcha . "*") as $filecaptcha) {
            /*** if file is 24 hours (86400 seconds) old then delete it ***/
            if (filemtime($filecaptcha) < time() - 3600) { // 1 hour
                unlink($filecaptcha);
            }
        }

        $vals = array(
            'img_path'     => './captcha/',
            'img_url'     => base_url() . 'captcha/',
            'font_path' => FCPATH . 'assets/font/ocraextended.ttf',
            'font_size'     => 13,
            'img_id'        => 'captcha-img',
            'img_height' => 40,
            //            'img_width'     => '100%',
            'border' => 2,
            'word_length'   => 5,
            'expiration' => 3600,
            'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(173, 216, 230)
            ),
            'pool'          => '123456789',
        );
        $cap = create_captcha($vals);


        $this->session->set_userdata('mycaptcha', $cap['word']);
        return $cap['image'];
    }

    public function reload_captcha()
    {
        $new_captcha = $this->get_captcha();
        echo "" . $new_captcha;
    }

    public function validate_captcha()
    {
        if ($this->input->post('captcha') !=  $this->session->userdata('mycaptcha')) {
            $this->session->set_flashdata('capca_error', 'show');
            $this->form_validation->set_message('validate_captcha', 'Captcha Yang di Input Tidak Benar! ');
            return false;
        } else {
            return true;
        }
    }
    public function cek_captcha()
    {
        if ($this->input->post('captcha') !=  $this->session->userdata('mycaptcha')) {
            $this->session->set_flashdata('capca_error', 'show');
            $this->form_validation->set_message('validate_captcha', 'Captcha Yang di Input Tidak Benar! ');
            echo 'false';
        } else {
            echo 'true';
        }
    }

    private function _Modal($title, $pesan, $href)
    {
        $this->session->set_flashdata('modal', '
                <div class="modal fade" data-backdrop="static" id="ModalAlert" tabindex="-1" role="dialog" aria-labelledby="ModalAlertLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalAlertLabel">' . $title . '</h5>
                            </div>
                            <div class="modal-body">
                                <p>' . $pesan . '</p>
                            </div>
                            <div class="modal-footer">
                                <a href="' . base_url('main') . '" class="btn btn-secondary">Ke Beranda</a>
                                <a href="' . $href . '" class="btn btn-primary">Cek Data Pengajuan</a>
                            </div>
                        </div>
                    </div>
                </div>');
    }



    private function _Swall($type, $title, $text, $txtConfirm, $txtCancel, $showCancel, $isConfirm, $elsConfirm)
    {
        $this->session->set_flashdata('swall', 'swal({
            title: "' . $title . '",
            text: "' . $text . '",
            type: "' . $type . '",
            showCancelButton: false,
            confirmButtonText: "' . $txtCancel . '",
            cancelButtonText: "' . $txtConfirm . '",
        }, function(isConfirm) {
            if (isConfirm) { ' .
            $isConfirm
            . '} else { ' .
            $elsConfirm
            . '}
        });');
    }

    private function _Swall_koneksi($type, $title, $text, $txtConfirm, $txtCancel, $showCancel, $isConfirm, $elsConfirm)
    {

        //        $this->_Swall('success', 'Pengajuan Berhasil...!!', 'Simpan Nomor Registrasi : ' . $this->session->userdata('noRegSukses') . 'dan Silahkan Cek Email', 'Beranda', 'Cek Data', false, 'window.location.href ="' . base_url('main/cekdata') . '"', 'window.location.href ="' . base_url('main') . '"');

        $this->session->set_userdata('swall_koneksi', 'swal({
            title: "' . $title . '",
            text: "' . $text . '",
            type: "' . $type . '",
            showCancelButton: false,
            confirmButtonText: "' . $txtCancel . '",
            cancelButtonText: "' . $txtConfirm . '",
        },
        function(isConfirm) {
            if (isConfirm) { ' .
            $isConfirm
            . '} else { ' .
            $elsConfirm
            . '}
        });');
    }


    private function _Swall2($type, $title, $text, $txtConfirm, $ID)
    {
        $this->session->set_flashdata('swall', 'swal({
            title: "' . $title . '",
            text: "' . $text . '",
            type: "' . $type . '",
            showCancelButton: false,
            confirmButtonText: "' . $txtConfirm . '",
        });
        document.getElementById("' . $ID . '").scrollIntoView();');
    }
}
