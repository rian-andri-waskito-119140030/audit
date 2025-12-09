<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Main_web extends CI_Model
{
    private $_client;
    //wget -q -O - https://pake-oli.lampungselatankab.go.id/scheduller >/dev/null 2>&1	
    public function __construct()
    {
        $this->db->query("SET time_zone='+07:00'");
    }
    public function getIdWeb()
    {
        $data = $this->db->get('web_setting')->row_array();
        return $data;
    }

    public function getAllMenu()
    {
        $this->db->select('*');
        $this->db->from('web_group_layanan');
        $this->db->order_by('id_group', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getDataUpdate()
    {
        $this->db->select('count(*) as count, status ');
        $this->db->from('update_nik');
        $this->db->group_by('status');
        $data[0] = $this->db->get()->result_array();
        $this->db->select('count(*) as count ');
        $this->db->from('update_nik');
        $data[1] = $this->db->get()->result_array();
        $res['all'] = $data[1][0]['count'];
        $res['success'] = 0;
        $res['failed'] = 0;
        $dd = $data[0];
        foreach ($dd as $kDD) {
            if ($kDD['status'] == 0)
                $res['failed'] = $kDD['count'];
            if ($kDD['status'] == 1)
                $res['success'] = $kDD['count'];
        };
        return $res;
    }

    public function getThisLay($link, $group, $target)
    {
        // $dd = [1, 2];
        $return = [];

        $this->db->select('id_lay, nama_layanan, target, opsi_ambil, cetak_mandiri, nama_singkat, link_group, web_layanan.id_group as id_group, notif_form, deskripsi, link, icon, warna, form_tambahan');
        $this->db->from('web_layanan');
        $this->db->join('web_group_layanan', 'web_group_layanan.id_group = web_layanan.id_group');
        // $this->db->where_in('web_layanan.target', $target);
        $this->db->where('web_layanan.active', 'Y');
        $this->db->where('web_group_layanan.link_group', $group);
        $this->db->where('web_layanan.link', $link);
        $data = $this->db->get()->row_array();
        $tgr = explode(',', $data['target']);
        foreach ($tgr as $k) {
            foreach ($target as $t) {
                if ($k == $t) {
                    $return = $data;
                    break;
                }
            }
        }
        return $return;
    }

    public function getThisLayInstansi($link, $group, $target, $role_id)
    {
        // $dd = [1, 2];
        $return = [];

        $this->db->select('web_layanan.id_lay, nama_layanan, target, opsi_ambil, cetak_mandiri, nama_singkat, link_group, web_layanan.id_group as id_group, notif_form, deskripsi, link, icon, warna, form_tambahan');
        $this->db->from('web_layanan');
        $this->db->join('web_group_layanan', 'web_group_layanan.id_group = web_layanan.id_group');
        $this->db->join('admin_user_layanan', 'admin_user_layanan.id_lay = web_layanan.id_lay');
        // $this->db->where_in('web_layanan.target', $target);
        $this->db->where_in('admin_user_layanan.role_id', $role_id);
        $this->db->where('web_layanan.active', 'Y');
        $this->db->where('web_group_layanan.link_group', $group);
        $this->db->where('web_layanan.link', $link);
        $data = $this->db->get()->row_array();
        $tgr = explode(',', $data['target']);
        foreach ($tgr as $k) {
            foreach ($target as $t) {
                if ($k == $t) {
                    $return = $data;
                    break;
                }
            }
        }
        return $return;
    }

    public function getAllLay()
    {
        $this->db->select('id_lay, nama_layanan, target, web_group_layanan.nama_group, active,nama_singkat, link_group, web_layanan.id_group as id_group, deskripsi, link,  icon, warna, form_tambahan');
        $this->db->from('web_layanan');
        $this->db->join('web_group_layanan', 'web_group_layanan.id_group = web_layanan.id_group');
        $this->db->where('web_layanan.active', 'Y');
        // $this->db->where_in('web_layanan.target', $target);
        $this->db->order_by('id_group', 'ASC');
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_layanan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }
    public function getAllLayInstansi()
    {

        $this->db->select('web_layanan.id_lay, nama_layanan, target, web_group_layanan.nama_group, active,nama_singkat, link_group, web_layanan.id_group as id_group, deskripsi, link,  icon, warna, form_tambahan');
        $this->db->from('web_layanan');
        $this->db->join('web_group_layanan', 'web_group_layanan.id_group = web_layanan.id_group');
        // $this->db->join('admin_user_layanan', 'admin_user_layanan.id_lay = web_layanan.id_lay');
        $this->db->where('web_layanan.active', 'Y');
        // $this->db->where('admin_user_layanan.role_id', $role_id);
        // $this->db->where_in('web_layanan.target', $target);
        $this->db->order_by('id_group', 'ASC');
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_layanan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }


    public function getAlasanLay($id_lay)
    {
        $this->db->select('*');
        $this->db->from('web_alasan_layanan');
        $this->db->where('id_lay', $id_lay);
        $this->db->where('active', 'Y');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListBerita()
    {
        $this->db->select('web_berita.*, f_tgl_lengkap(tgl_post) as tanggal');
        $this->db->from('web_berita');
        $this->db->where('active', 1);

        $this->db->order_by('tgl_post', 'DESC');
        $data = $this->db->get()->result_array();
        return $data;
    }


    public function ListRespSKM()
    {

        $dataSKM = $this->db->select('*')
            ->from('web_form_skm')
            ->order_by('tanggal')
            ->get()->result_array();
        return $dataSKM;
        // $sql = 'select * from web_pemohon where proses not in (3,10,7,8,11)';
        // $layBelum = $this->db->query($sql)->result_array();
    }
    public function GrafikSkm()
    {
        $index = 1 / 9;

        $getTahun = $this->db
            ->select(
                '
            tahun,semester
            , count(*) as pembagi
            , sum(tanya_1) as U1
            , sum(tanya_2) as U2
            , sum(tanya_3) as U3
            , sum(tanya_4) as U4
            , sum(tanya_5) as U5
            , sum(tanya_6) as U6
            , sum(tanya_7) as U7
            , sum(tanya_8) as U8
            , sum(tanya_9) as U9
            '
            )
            ->from('web_form_skm')
            ->group_by('tahun, semester')
            ->order_by('tahun')->get()->result_array();
        for ($i = 0; $i < count($getTahun); $i++) {
            $dataRes[$i]['id_skm'] =  $getTahun[$i]['tahun'] . '0' . $getTahun[$i]['semester'];
            $dataRes[$i]['semester'] =  $getTahun[$i]['semester'];
            $dataRes[$i]['tahun'] =  $getTahun[$i]['tahun'];
            $dataRes[$i]['nilai'] = (
                ($getTahun[$i]['U1'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U2'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U3'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U4'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U5'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U6'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U7'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U8'] / $getTahun[$i]['pembagi'] * $index)
                +
                ($getTahun[$i]['U9'] / $getTahun[$i]['pembagi'] * $index))
                * 25;
        }



        return $dataRes;
    }

    public function ListSKmDetailPer($tahun, $smt)
    {
        $idxLay = 9;
        $index = 1 / $idxLay;
        $resArray = array();
        $getTahun = $this->db->select('tahun')
            ->where(['tahun' => $tahun, 'semester' => $smt])
            ->from('web_form_skm')
            ->group_by('tahun')
            ->order_by('tahun')
            ->get()->result_array();

        $getList = $this->db
            ->select(
                'tahun, semester, tanggal
            , tanya_1 as U1
            , tanya_2 as U2
            , tanya_3 as U3
            , tanya_4 as U4
            , tanya_5 as U5
            , tanya_6 as U6
            , tanya_7 as U7
            , tanya_8 as U8
            , tanya_9 as U9'
            )
            ->where(['tahun' => $tahun, 'semester' => $smt])
            ->from('web_form_skm')
            ->order_by('tanggal')->get()->result_array();
        $getRekapLayanan = $this->db
            ->select('jns_layanan,tahun, semester, skm_layanan.id_lay_skm, skm_layanan.nama_lay, count(*) hit')
            ->join('web_form_skm', 'skm_layanan.id_lay_skm = web_form_skm.jns_layanan')
            ->from('skm_layanan')
            ->where(['tahun' => $tahun, 'semester' => $smt])
            ->group_by('jns_layanan')
            ->order_by('tahun, semester')->get()->result_array();

        $getRekap = $this->db
            ->select(
                '
            tahun, semester
            , count(*) as pembagi
            , sum(tanya_1) as U1
            , sum(tanya_2) as U2
            , sum(tanya_3) as U3
            , sum(tanya_4) as U4
            , sum(tanya_5) as U5
            , sum(tanya_6) as U6
            , sum(tanya_7) as U7
            , sum(tanya_8) as U8
            , sum(tanya_9) as U9
            '
            )
            ->where(['tahun' => $tahun, 'semester' => $smt])
            ->from('web_form_skm')
            ->group_by('tahun, semester')
            ->order_by('tahun, semester')->get()->result_array();



        for ($i = 0; $i < count($getRekap); $i++) {

            // $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['sumary'][] =  $getRekap[$i]['tahun'] . '0' . $getRekap[$i]['semester'];
            // $dataRes[$i]['semester'] =  $getRekap[$i]['semester'];
            // $dataRes[$i]['tahun'] =  $getRekap[$i]['tahun'];
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U1'] =  round($getRekap[$i]['U1'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U1'] =  round($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U1'] =   round($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index, 2);


            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U2'] =  round($getRekap[$i]['U2'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U2'] =   round($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U2'] =   round($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U3'] =  round($getRekap[$i]['U3'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U3'] =   round($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U3'] =  round($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U4'] =  round($getRekap[$i]['U4'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U4'] =  round($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U4'] =  round($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U5'] =  round($getRekap[$i]['U5'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U5'] =   round($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U5'] =   round($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U6'] =  round($getRekap[$i]['U6'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U6'] =  round($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U6'] =  round($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U7'] =  round($getRekap[$i]['U7'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U7'] =  round($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U7'] = round($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U8'] =  round($getRekap[$i]['U8'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U8'] =  round($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U8'] = round($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U9'] =  round($getRekap[$i]['U9'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U9'] =  round($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U9'] =   round($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['TOTAL'] =
                round(
                    ($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index),
                    2
                );

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['IKM'] =
                round(
                    (
                        (($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index)) * 25),
                    2
                );
        }


        foreach ($getTahun as $keTahun => $kTahun) {

            for ($i = 0; $i < count($getList); $i++) {

                if ($tahun == $getList[$i]['tahun'] && $getList[$i]['semester'] = $smt) {
                    $resArray['detail'][] = $getList[$i];

                    $resArray['total'] = $dataRes[$getList[$i]['tahun']][$getList[$i]['semester']]['total'];
                }
            }
            // for ($i = 0; $i < count($dataPelayanSKM); $i++) {
            //     $resArray['rekap_pelayanan'][$getRekapLayanan[$i]['id_lay_skm']] = $getRekapLayanan[$i];
            //     if ($tahun == $getRekapLayanan[$i]['tahun'] && $getRekapLayanan[$i]['semester'] = $smt) {
            //     }
            // }
        }
        $dataPelayanSKM = $this->db->order_by('urutan', 'asc')->get('skm_layanan')->result_array();
        $llo = 1;

        foreach ($dataPelayanSKM as $vpskm) {
            $resArray['rekap_pelayanan'][$llo] = [
                'nama_layanan' => $vpskm['nama_lay'],
                'hit' => 0
            ];

            foreach ($getRekapLayanan as $drskm) {
                if ($drskm['id_lay_skm'] == $vpskm['id_lay_skm']) {

                    $resArray['rekap_pelayanan'][$llo] = [
                        'nama_layanan' => $vpskm['nama_lay'],
                        'hit' => $drskm['hit']
                    ];

                    break;
                }
            }
            $llo++;
        }
        // for ($i = 0; $i < count($dataPelayanSKM); $i++) {

        //     $resArray['rekap_pelayanan'][$dataPelayanSKM[$i]['id_lay_skm']]['nama_layanan'] = $dataPelayanSKM[$i]['nama_lay'];


        //     if (
        //         $tahun == $getRekapLayanan[$i]['tahun'] && $getRekapLayanan[$i]['semester'] = $smt

        //         && $getRekapLayanan[$i]['id_lay_skm'] > $dataPelayanSKM[$i]['id_lay_skm']
        //     ) {
        //         $resArray['rekap_pelayanan'][$dataPelayanSKM[$i]['id_lay_skm']]['hit'] = $getRekapLayanan[$i]['hit'];
        //     } else {
        //         $resArray['rekap_pelayanan'][$dataPelayanSKM[$i]['id_lay_skm']]['hit'] = 0;
        //     }
        // }

        return $resArray;
    }


    public function ListSKmDetail()
    {
        $index = 1 / 9;
        $resArray = array();
        $getTahun = $this->db->select('tahun')->from('web_form_skm')->group_by('tahun')
            ->order_by('tahun')
            ->get()->result_array();



        $getList = $this->db
            ->select(
                'tahun,semester, tanggal
            , tanya_1 as U1
            , tanya_2 as U2
            , tanya_3 as U3
            , tanya_4 as U4
            , tanya_5 as U5
            , tanya_6 as U6
            , tanya_7 as U7
            , tanya_8 as U8
            , tanya_9 as U9'
            )
            ->from('web_form_skm')
            ->order_by('tanggal')->get()->result_array();

        $getRekap = $this->db
            ->select(
                '
            tahun,semester
            , count(*) as pembagi
            , sum(tanya_1) as U1
            , sum(tanya_2) as U2
            , sum(tanya_3) as U3
            , sum(tanya_4) as U4
            , sum(tanya_5) as U5
            , sum(tanya_6) as U6
            , sum(tanya_7) as U7
            , sum(tanya_8) as U8
            , sum(tanya_9) as U9
            '
            )
            ->from('web_form_skm')
            ->group_by('tahun, semester')
            ->order_by('tahun, semester')->get()->result_array();

        for ($i = 0; $i < count($getRekap); $i++) {
            // $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['sumary'][] =  $getRekap[$i]['tahun'] . '0' . $getRekap[$i]['semester'];
            // $dataRes[$i]['semester'] =  $getRekap[$i]['semester'];
            // $dataRes[$i]['tahun'] =  $getRekap[$i]['tahun'];
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U1'] =  round($getRekap[$i]['U1'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U1'] =  round($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U1'] =   round($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U2'] =  round($getRekap[$i]['U2'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U2'] =   round($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U2'] =   round($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U3'] =  round($getRekap[$i]['U3'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U3'] =   round($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U3'] =  round($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U4'] =  round($getRekap[$i]['U4'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U4'] =  round($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U4'] =  round($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U5'] =  round($getRekap[$i]['U5'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U5'] =   round($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U5'] =   round($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U6'] =  round($getRekap[$i]['U6'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U6'] =  round($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U6'] =  round($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U7'] =  round($getRekap[$i]['U7'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U7'] =  round($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U7'] = round($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U8'] =  round($getRekap[$i]['U8'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U8'] =  round($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U8'] = round($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NU']['U9'] =  round($getRekap[$i]['U9'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR']['U9'] =  round($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'], 2);
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['U9'] =   round($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index, 2);

            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['TOTAL'] =
                round(
                    ($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index)
                        +
                        ($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index),
                    2
                );
            $dataRes[$getRekap[$i]['tahun']][$getRekap[$i]['semester']]['total']['NRR_IDX']['IKM'] =
                round(
                    (
                        (($getRekap[$i]['U1'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U2'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U3'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U4'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U5'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U6'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U7'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U8'] / $getRekap[$i]['pembagi'] * $index)
                            +
                            ($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index)) * 25),
                    2
                );
            // round($getRekap[$i]['U9'] / $getRekap[$i]['pembagi'] * $index, 2);
        }

        foreach ($getTahun as $keTahun => $kTahun) {



            for ($i = 0; $i < count($getList); $i++) {
                if ($kTahun['tahun'] == $getList[$i]['tahun']) {
                    $resArray[$kTahun['tahun']][$getList[$i]['semester']]['detail'][] = $getList[$i];
                    $resArray[$kTahun['tahun']][$getList[$i]['semester']]['total'] =
                        $dataRes[$getList[$i]['tahun']][$getList[$i]['semester']]['total'];
                }
            }
        }

        return $resArray;
    }
    public function CekResponden($is_session)
    {
        $this->db->select('web_form_skm.*');
        $this->db->from('web_form_skm');
        $this->db->or_where('session_id', $is_session);
        $data = $this->db->get()->num_rows();
        if ($data > 0)
            return false;
        else
            return true;
    }
    public function InsSKM($post, $id_session)
    {
        $tanggal = htmlspecialchars($post['tanggal'], true);
        $jam = htmlspecialchars($post['jam'], true);
        $umur = htmlspecialchars($post['umur'], true);
        $bulan = number_format(substr($tanggal, 3, 2));
        if ($bulan <= 6)
            $semester = 1;
        else
            $semester = 2;

        $kelamin = htmlspecialchars($post['kelamin'], true);
        $pddkn = htmlspecialchars($post['pddkn'], true);
        $pkrjn = htmlspecialchars($post['pkrjn'], true);
        $jns_layanan = htmlspecialchars($post['jns_layanan'], true);
        $tanya_1 = htmlspecialchars($post['tanya_1'], true);
        $tanya_2 = htmlspecialchars($post['tanya_2'], true);
        $tanya_3 = htmlspecialchars($post['tanya_3'], true);
        $tanya_4 = htmlspecialchars($post['tanya_4'], true);
        $tanya_5 = htmlspecialchars($post['tanya_5'], true);
        $tanya_6 = htmlspecialchars($post['tanya_6'], true);
        $tanya_7 = htmlspecialchars($post['tanya_7'], true);
        $tanya_8 = htmlspecialchars($post['tanya_8'], true);
        $tanya_9 = htmlspecialchars($post['tanya_9'], true);
        $tanya_1 = substr($tanya_1, -1);
        $tanya_2 = substr($tanya_2, -1);
        $tanya_3 = substr($tanya_3, -1);
        $tanya_4 = substr($tanya_4, -1);
        $tanya_5 = substr($tanya_5, -1);
        $tanya_6 = substr($tanya_6, -1);
        $tanya_7 = substr($tanya_7, -1);
        $tanya_8 = substr($tanya_8, -1);
        $tanya_9 = substr($tanya_9, -1);

        $dataInst = [
            'session_id' => $id_session,
            'tanggal' => $tanggal,
            'semester' => $semester,
            'tahun' => substr($tanggal, 6, strlen($tanggal)),
            'jam' => $jam,
            'umur' => $umur,
            'kelamin' => $kelamin,
            'pddkn' => $pddkn,
            'pkrjn' => $pkrjn,
            'jns_layanan' => $jns_layanan,
            'tanya_1' => $tanya_1,
            'tanya_2' => $tanya_2,
            'tanya_3' => $tanya_3,
            'tanya_4' => $tanya_4,
            'tanya_5' => $tanya_5,
            'tanya_6' => $tanya_6,
            'tanya_7' => $tanya_7,
            'tanya_8' => $tanya_8,
            'tanya_9' => $tanya_9,
        ];

        $this->db->insert('web_form_skm', $dataInst);
        $insCek = $this->db->affected_rows();

        if ($insCek < 1)
            return false;
        else
            return true;
    }


    public function InsAduan($post)
    {
        $nama_lgkp = htmlspecialchars($post['nama_lgkp'], true);
        $email = htmlspecialchars($post['email'], true);
        $no_telp = htmlspecialchars($post['no_telp'], true);
        $alamat = htmlspecialchars($post['alamat'], true);
        $judul = htmlspecialchars($post['judul'], true);
        $isi_aduan = htmlspecialchars($post['isi_aduan'], true);

        $dataIns = [
            'nama' => $nama_lgkp,
            'email' => $email,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'judul' => $judul,
            'isi' => $isi_aduan,
        ];

        $this->db->insert('web_pengaduan', $dataIns);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    public function getBerita($id_berita)
    {
        $this->db->select('web_berita.*, f_tgl_lengkap(tgl_post) as tanggal');
        $this->db->from('web_berita');
        $this->db->where('active', 1);
        $this->db->where('id', $id_berita);

        $this->db->order_by('tgl_post', 'ASC');
        $data = $this->db->get()->row_array();
        return $data;
    }
    public function setRatePemohon($no_reg, $rate)
    {
        $this->db->select('*');
        $this->db->from('web_pemohon');
        $this->db->where('is_rate>', 1);
        $this->db->where('no_reg', $no_reg);

        $cekRate = $this->db->get()->num_rows();
        if ($cekRate > 0) {
            return false;
        } else {
            $dataInsert = [
                'id_key' => $no_reg,
                'value' => $rate
            ];
            $this->db->replace('web_rating', $dataInsert);
            $is_sukses = $this->db->affected_rows();
            if ($is_sukses > 0) {
                $this->db->set('is_rate', $rate);
                $this->db->where('no_reg', $no_reg);
                $this->db->update('web_pemohon');
                $is_update = $this->db->affected_rows();
                if ($is_update > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getAlasanLayEdit($id_lay, $id_alasan)
    {
        $this->db->select('*');
        $this->db->from('web_alasan_layanan');
        $this->db->where('id_lay', $id_lay);
        $this->db->where('id_alasan', $id_alasan);
        $this->db->where('active', 'Y');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListKab($no_prop)
    {
        $this->db->select('*');
        $this->db->from('setup_kab');
        $this->db->where('no_prop', $no_prop);
        $this->db->order_by('no_prop', 'ASC');
        $this->db->order_by('no_kab', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListKecBy($no_prop, $no_kab, $no_kec)
    {
        $this->db->select('*');
        $this->db->from('setup_kec');
        $this->db->where('no_prop', $no_prop);
        $this->db->where('no_kab', $no_kab);
        $this->db->where('no_kec', $no_kec);
        $this->db->order_by('no_kec', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }
    public function getListKec($no_prop, $no_kab)
    {
        $this->db->select('*');
        $this->db->from('setup_kec');
        $this->db->where('no_prop', $no_prop);
        $this->db->where('no_kab', $no_kab);
        $this->db->order_by('no_kec', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListKel($no_prop, $no_kab, $no_kec)
    {
        $this->db->select('*');
        $this->db->from('setup_kel');
        $this->db->where('no_prop', $no_prop);
        $this->db->where('no_kab', $no_kab);
        $this->db->where('no_kec', $no_kec);
        $this->db->order_by('no_prop', 'ASC');
        $this->db->order_by('no_kab', 'ASC');
        $this->db->order_by('no_kec', 'ASC');
        $this->db->order_by('no_kel', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }


    public function getListKelBy($no_prop, $no_kab, $no_kec, $no_kel)
    {
        $this->db->select('*');
        $this->db->from('setup_kel');
        $this->db->where('no_prop', $no_prop);
        $this->db->where('no_kab', $no_kab);
        $this->db->where('no_kec', $no_kec);
        $this->db->where('no_kel', $no_kel);
        $this->db->order_by('no_prop', 'ASC');
        $this->db->order_by('no_kab', 'ASC');
        $this->db->order_by('no_kec', 'ASC');
        $this->db->order_by('no_kel', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListTanggalRekam($id_loket)
    {
        $this->db->select('id_tgl_rekam, id_loket, kuota, sisa, f_tgl_lengkap(tanggal) as tanggal_lgkp');
        $this->db->from('web_tanggal_rekam');
        $this->db->where('id_loket', $id_loket);
        $this->db->where('sisa>', '0');
        $this->db->where('tanggal >= DATE_FORMAT(CURDATE(), "%Y-%m-%d")');
        $this->db->order_by('tanggal', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getLoketList()
    {
        $this->db->select('web_loket_rekam.*');
        $this->db->from('web_loket_rekam');
        $this->db->join('web_tanggal_rekam', 'web_tanggal_rekam.id_loket = web_loket_rekam.id_loket');
        $this->db->where('web_loket_rekam.active', 'Y');
        $this->db->where('web_tanggal_rekam.sisa>0');
        $this->db->where('web_tanggal_rekam.tanggal >= DATE_FORMAT(CURDATE(), "%Y-%m-%d")');
        $this->db->order_by('nama_loket', 'ASC');
        $this->db->group_by('web_loket_rekam.id_loket');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getListTanggalRekamAdm($id_loket)
    {
        $this->db->select('id_tgl_rekam, id_loket, kuota, sisa, f_tgl_lengkap(tanggal) as tanggal');
        $this->db->from('web_tanggal_rekam');
        $this->db->where('id_loket', $id_loket);
        $this->db->order_by('tanggal', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function cekAdaLayMandiri()
    {
        $ttt = 0;
        $tes = $this->db->get_where('web_layanan')->result_array();
        $tes2 = array();
        foreach ($tes as $key => $vale) {
            // echo count(json_decode($vale['opsi_ambil']));
            if (json_decode($vale['opsi_ambil']))
                $ttt++;
            // print_r(json_decode());
        }
        if ($ttt > 0)
            return true;
        else
            return false;
    }

    public function templateEmail($no_reg)
    {
        $nama_app = $this->getIdWeb()['nama_aplikasi'];
        $nama_kab = $this->getIdWeb()['nama_kab'];
        $nama_satker = $this->getIdWeb()['nama_satker'];
        $nama_prop = $this->getIdWeb()['nama_prop'];
        $nomor_wa = $this->getIdWeb()['whatsapp'];
        $email_satker = $this->getIdWeb()['email'];
        $nomor_telp = $this->getIdWeb()['no_telp'];
        $cekData = md5($no_reg);
        $urlGen = base_url('main/cekdata/') . $cekData;
        $urlShort = $urlGen;

        $this->db->select('nik
        , nama_lgkp
        , nama_layanan
        , jenis_alasan
        , proses
        , email
        , fcm_token
        , no_kk
        , cttn_petugas
        , cetak_mandiri
        , opsi_ambil
        , f_tgl_lengkap(tgl_pengajuan) as tgl_pengajuan
        , DATE_FORMAT(tgl_pengajuan, "%H:%i:%s") as jam');
        $this->db->from('web_pemohon');
        $this->db->where('no_reg', $no_reg);

        $data_pemohon = $this->db->get()->row_array();

        $id_proses = $data_pemohon['proses'];
        if ($id_proses == 1 or $id_proses == 9) {
            $pembuka = 'Terima Kasih atas kepercayaan Anda, telah menggunakan fasilitas ';
        } else {
            $pembuka = 'Kami telah melakukan proses pada Pengajuan Anda di ';
        }
        $histori =  $this->main_web->getHistoriEmail($no_reg, $id_proses);

        // if ($id_proses == 10) {
        //     $histori =  $this->main_web->getHistoriWithRekam($no_reg);
        // } else {
        //     $histori =  $this->main_web->getHistoriEmail($no_reg, $id_proses);
        // }

        $ketProses = $this->db->get_where('web_proses', ['id' =>  $id_proses])->row_array();

        // Email body content
        $mailContent = '<table width="600"><tbody>';
        $mailContent .= '<tr><td colspan="3">{(berisalam)},</td></tr>';
        $mailContent .= '<tr><td colspan="3">&nbsp;</td></tr>';
        $mailContent .= '<tr><td colspan="3">Kepada Yth, <strong>' . $data_pemohon['nama_lgkp'] . '</strong></td></tr>';
        $mailContent .= '<tr><td colspan="3">' . $pembuka . ' Aplikasi <strong>' . $nama_app . ' ' .  $nama_kab . '.</strong></td></tr>';
        $mailContent .= '<tr>
                        <td colspan="3">Berikut merupakan ringkasan informasi pengajuan yang telah Anda lakukan dan sudah di Rekam oleh System Kami :</td>
                        </tr>';
        $mailContent .= '<tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';

        $mailContent .= '
                        <tr>
                        <td width="30%">Nomor Registrasi</td>
                        <td>:</td>
                        <td><strong>' . $no_reg . '</strong></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">NIK</td>
                        <td>:</td>
                        <td>' . $data_pemohon['nik'] . '</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Nama Lengkap</td>
                        <td>:</td>
                        <td>' . $data_pemohon['nama_lgkp'] . '</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Waktu Pengajuan</td>
                        <td>:</td>
                        <td>' . $data_pemohon['tgl_pengajuan'] . ' Jam : ' . $data_pemohon['jam'] . '</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Jenis Layanan</td>
                        <td>:</td>
                        <td>' . $data_pemohon['nama_layanan'] . ' (' . $data_pemohon['jenis_alasan'] . ')' . '</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Status</td>
                        <td>:</td>
                        <td><strong>' . $ketProses['proses'] . '</strong></td>
                        </tr>';

        $mailContent .= '<tr>
                        <td width="30%">Ket Status</td>
                        <td>:</td>
                        <td>' . $ketProses['keterangan'] . '</td>
                        </tr>';

        if ($data_pemohon['opsi_ambil'] == 1)
            if ($data_pemohon['cetak_mandiri'] == 1) {
                $mailContent .= '<tr>
                        <td width="30%">Layanan Cetak Mandiri</td>
                        <td>:</td>
                        <td>Disetujui</td>
                        </tr>';
            } else if ($data_pemohon['cetak_mandiri'] == 2) {
                $mailContent .= '<tr>
                        <td width="30%">Cetak Mandiri</td>
                        <td>:</td>
                        <td>Di Tolak</td>
                        </tr>';
            }

        if ($id_proses == 12)
            if ($data_pemohon['opsi_ambil'] == 1)
                if ($data_pemohon['cetak_mandiri'] == 1) {
                    $mstrPDDK = $this->db->get_where('master_penduduk', ['nik' => $data_pemohon['nik'], 'no_kk' => $data_pemohon['no_kk']])->row_array();
                    $mailContent .= '<tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
                    $mailContent .= '<tr>
                        <td colspan="3" style="text-align:center;">Silahkan Klik LINK CETAK MANDIRI dibawah ini Untuk Melakukan Cetak Mandiri Dokumen Yang di Ajukan</td>
                        </tr>';
                    $mailContent .= '<tr>
                        <td colspan="3" style="text-align:center; font-size: 16px;"><strong><a title="Cetak Mandiri" href="' . base_url('mandiri/cetak/') . encrypt_url($data_pemohon['nik'] . $data_pemohon['no_kk']) . '/' . encrypt_url($no_reg) . '" target="_blank" rel="noopener">CETAK MANDIRI</a> </strong></td>
                        </tr>';
                    $mailContent .= '
                        <tr>
                        <td width="30%" colspan="3" style="text-align:center;"><strong>Dan gunakan PIN/Password pada saat akan mencetak dibawah ini Untuk </strong></td>
                        </tr>';
                    $mailContent .= '
                        <tr>
                        <td width="30%" colspan="3" style="text-align:center; font-size: 14px;"><span style="border:1px solid; padding:2px;"><strong>' . $mstrPDDK['pin'] . '</strong></span></td>
                        </tr>';
                }

        $mailContent .= '<tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';

        if ($histori['tanggal'] != '' and $histori['jam'] != '' and $id_proses != 9 and $id_proses != 1) {
            $mailContent .= '<tr>
                            <td width="30%">Waktu Update</td>
                            <td>:</td>
                            <td>' . $histori['tanggal'] . ' Jam : ' . $histori['jam'] . '</td>
                            </tr>';
        }
        if ($histori['catatan'] != '' and $id_proses != 9) {
            $mailContent .= '<tr>
                            <td width="30%">Catatan Petugas</td>
                            <td>:</td>
                            <td><strong>' . $histori['catatan'] . '</strong></td>
                            </tr>';
        }
        if ($id_proses == 7) {
            $mailContent .= '<tr>
                            <td width="30%">Loket Pengambilan :</td>
                            <td>:</td>
                            <td><strong>' . $histori['loket_pengambilan'] . '</strong></td>
                            </tr>';
        }

        if ($id_proses == 15) {
            $dYanduk = $this->db->get_where('yanduk_satlantas', ['no_reg' => $no_reg])->row_array();
            $this->load->helper('tgl_indo');
            if ($dYanduk['tgl_kunjung_apr'])
                $tgl_apr = longdate_indo(date('Y-m-d', strtotime($dYanduk['tgl_kunjung_apr'])));
            else
                $tgl_apr = '-';

            $mailContent .= '
                <tr>
                <td width="30%">Tanggal Rekam</td>
                <td>:</td>
                <td><strong>' . $tgl_apr . '</strong></td>
                </tr>';
        }
        if ($id_proses == 10) {

            $this->db->select('perekaman.*, f_tgl_lengkap(tgl_rekam) as tanggal_rekam');
            $this->db->from('perekaman');
            $this->db->where('no_reg', $no_reg);
            $data_rekam = $this->db->get()->row_array();

            $mailContent .= '
                            <tr>
                            <td width="30%">Tanggal Rekam</td>
                            <td>:</td>
                            <td><strong>' . $data_rekam['tanggal_rekam'] . '</strong></td>
                            </tr>';
            $mailContent .= '
                            <tr>
                            <td width="30%">Loket Perekaman</td>
                            <td>:</td>
                            <td><strong>' . $data_rekam['nama_loket'] . '</strong></td>
                            </tr>';
            $mailContent .= '
                            <tr>
                            <td width="30%">Nomor Antrian</td>
                            <td>:</td>
                            <td><strong>' . $data_rekam['urutan'] . '</strong></td>
                            </tr>';
        }
        $mailContent .= '
                        <tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">Untuk Mengetahui Detail Selengkapnya tentang Proses Pelayanan tersebut, silahkan Klik Link :&nbsp;<a title="Cek Data" href="' . $urlShort . '" target="_blank" rel="noopener">Cek Data</a></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        Terima Kasih,
                        <br>
                        <br>
                        Hormat Kami,
                        <br>
                        </td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        <strong>
                        <h4 style="padding: 0; margin: 0;">' . $nama_app . ',</h4>
                        <h4 style="padding: 0; margin: 0;">' . $nama_satker . '</h3>
                        <h4 style="padding: 0; margin: 0;">' . $nama_kab . '</h3>
                        </strong>
                        <br>
                        <span>Whatsapp : ' . $nomor_wa . '</span>
                        <br>
                        <span>Nomor Telp : ' . $nomor_telp . '</span>
                        <br>
                        <span>email : ' . $email_satker . '</span>
                        <br>
                        <br>
                        <br>
                        </td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        <span style="font-size: medium;">
                        <span style="font-family: Calibri;">
                        <span style="font-size: small;">
                        <em><strong>Perhatian</strong>: E-mail ini (termasuk seluruh lampirannya, bila ada) hanya ditujukan kepada penerima yang tercantum di atas. Jika Anda bukan penerima yang dituju, maka Anda tidak diperkenankan untuk memanfaatkan, menyebarkan, mendistribusikan, atau menggandakan e-mail ini beserta seluruh lampirannya. Mohon kerjasamanya untuk segera memberitahukan kepada ' . $nama_satker . ' ' . $nama_kab . ' di kontak yang tercantum di atas serta menghapus e-mail ini beserta seluruh lampirannya. Semua pendapat yang ada dalam e-mail ini merupakan pendapat pribadi dari pengirim yang bersangkutan dan tidak serta merta mencerminkan pandangan ' . $nama_satker . ' ' . $nama_kab . ', kecuali telah terdapat kesepakatan antara pengirim dan penerima bahwa e-mail ini termasuk salah satu bentuk komunikasi kedinasan yang dapat diterima oleh kedua pihak. 
                        <br><br>
                        <strong>email ini tidak di monitoring, untuk itu JANGAN membalas dan mengirim email ke alamat e-mail ini</strong>
                        </em>
                        </span>
                        </span>
                        </span>
                        </td>
                        </tr>';
        $mailContent .= '
                        </tbody>
                        </table>';  //ini adalah isi/body email
        $email_subject = '[' . $nama_app . '] - Notifikasi Pengajuan';
        $dataEmail = [
            'no_reg' => $no_reg,
            'email_to' => html_entity_decode($data_pemohon['email']),
            'kriteria' => 'email_notif_pemohon',
            'proses_id' => $data_pemohon['proses'],
            'subject' => $email_subject,
            'body' =>  $mailContent,
        ];

        $this->db->insert('outbox_email', $dataEmail);
        return true;
    }

    public function templateEmailVerifikasi($nik, $no_kk, $resend)
    {
        $nama_app = $this->getIdWeb()['nama_aplikasi'];
        $nama_kab = $this->getIdWeb()['nama_kab'];
        $nama_satker = $this->getIdWeb()['nama_satker'];
        $nama_prop = $this->getIdWeb()['nama_prop'];
        $nomor_wa = $this->getIdWeb()['whatsapp'];
        $email_satker = $this->getIdWeb()['email'];
        $nomor_telp = $this->getIdWeb()['no_telp'];

        $urlToken = encrypt_url($nik . $no_kk);
        $urlGen = base_url('mandiri/validasi/token/') . $urlToken;
        $urlShort = $urlGen;

        $this->db->select('*');
        $this->db->from('master_penduduk');
        $this->db->where('nik', $nik);
        $this->db->where('no_kk', $no_kk);
        $data_penduduk = $this->db->get()->row_array();
        $PIN =  $data_penduduk['pin'];
        if ($resend)
            $pembuka = 'Permintaan Lupa PIN Layanan Mandiri pada ';
        else
            $pembuka = 'Terima Kasih atas kepercayaan Anda, telah menggunakan fasilitas ';
        // Email body content
        $mailContent = '<table width="600"><tbody>';
        $mailContent .= '<tr><td colspan="3">{(berisalam)},</td></tr>';
        $mailContent .= '<tr><td colspan="3">&nbsp;</td></tr>';
        $mailContent .= '<tr><td colspan="3">Kepada Yth, <strong>' . $data_penduduk['nama_lgkp'] . '</strong></td></tr>';
        $mailContent .= '<tr><td colspan="3">' . $pembuka . ' Aplikasi <strong>' . $nama_app . ' ' .  $nama_kab . '.</strong></td></tr>';
        if (!$resend) {
            $mailContent .= '<tr>
                            <td colspan="3">Berikut merupakan PIN Verifikasi sebagai PIN untuk Melakukan Proses Pelayanan Mandiri dan berikut informasi pengajuan Verifikasi yang telah Anda lakukan dan sudah di Rekam oleh System Kami :</td>
                            </tr>';
            $mailContent .= '<tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        }
        $mailContent .= '
                        <tr>
                        <td width="30%">NIK </td>
                        <td>:</td>
                        <td><strong>' . $data_penduduk['nik'] . '</strong></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Nomor KK</td>
                        <td>:</td>
                        <td>' . $data_penduduk['no_kk'] . '</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%">Nama Lengkap</td>
                        <td>:</td>
                        <td><strong>' . $data_penduduk['nama_lgkp'] . '</strong></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%" colspan="3" style="text-align:center; font-size: 18px;"><strong>PIN VERIFIKASI ANDA</strong></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td width="30%" colspan="3" style="text-align:center; font-size: 24px;"><span style="border:1px solid; padding:2px;"><strong>' . $data_penduduk['pin'] . '</strong></span></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';

        $mailContent .= '<tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">&nbsp;</td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">Untuk melakukan Verifikasi atau Lupa PIN , silahkan Klik Link :&nbsp;<a title="Verifikasi" href="' . $urlShort . '" target="_blank" rel="noopener">Verifikasi Sekarang</a></td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        Terima Kasih,
                        <br>
                        <br>
                        Hormat Kami,
                        <br>
                        </td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        <strong>
                        <h4 style="padding: 0; margin: 0;">' . $nama_app . ',</h4>
                        <h4 style="padding: 0; margin: 0;">' . $nama_satker . '</h3>
                        <h4 style="padding: 0; margin: 0;">' . $nama_kab . '</h3>
                        </strong>
                        <br>
                        <span>Whatsapp : ' . $nomor_wa . '</span>
                        <br>
                        <span>Nomor Telp : ' . $nomor_telp . '</span>
                        <br>
                        <span>email : ' . $email_satker . '</span>
                        <br>
                        <br>
                        <br>
                        </td>
                        </tr>';
        $mailContent .= '
                        <tr>
                        <td colspan="3">
                        <span style="font-size: medium;">
                        <span style="font-family: Calibri;">
                        <span style="font-size: small;">
                        <em><strong>Perhatian</strong>: E-mail ini (termasuk seluruh lampirannya, bila ada) hanya ditujukan kepada penerima yang tercantum di atas. Jika Anda bukan penerima yang dituju, maka Anda tidak diperkenankan untuk memanfaatkan, menyebarkan, mendistribusikan, atau menggandakan e-mail ini beserta seluruh lampirannya. Mohon kerjasamanya untuk segera memberitahukan kepada ' . $nama_satker . ' ' . $nama_kab . ' di kontak yang tercantum di atas serta menghapus e-mail ini beserta seluruh lampirannya. Semua pendapat yang ada dalam e-mail ini merupakan pendapat pribadi dari pengirim yang bersangkutan dan tidak serta merta mencerminkan pandangan ' . $nama_satker . ' ' . $nama_kab . ', kecuali telah terdapat kesepakatan antara pengirim dan penerima bahwa e-mail ini termasuk salah satu bentuk komunikasi kedinasan yang dapat diterima oleh kedua pihak. 
                        <br><br>
                        <strong>email ini tidak di monitoring, untuk itu JANGAN membalas dan mengirim email ke alamat e-mail ini</strong>
                        </em>
                        </span>
                        </span>
                        </span>
                        </td>
                        </tr>';
        $mailContent .= '
                        </tbody>
                        </table>';  //ini adalah isi/body email
        $email_subject = '[' . $nama_app . '] - PIN Verifikasi';

        $getOutbox = $this->db->get_where('outbox_email', ['no_reg' => $nik, 'is_sent' => 'U', 'kriteria' => 'email_validasi'])->row_array();
        if ($getOutbox) {
            $this->db->set('email_to', html_entity_decode($data_penduduk['email']));
            $this->db->set('body', $mailContent);
            $this->db->where('id_out_email', $getOutbox['id_out_email']);
            $this->db->update('outbox_email');
        } else {
            $dataEmail = [
                'no_reg' => $nik,
                'email_to' => html_entity_decode($data_penduduk['email']),
                'kriteria' => 'email_validasi',
                'is_sent' => 'U',
                'subject' => $email_subject,
                'body' =>  $mailContent,
            ];

            $this->db->insert('outbox_email', $dataEmail);
        }

        //// whatsapp
        $nama_app = $this->getIdWeb()['nama_aplikasi'];
        $nama_kab = $this->getIdWeb()['nama_kab'];
        // $cekData = $urlGen;
        // $urlGen = 'https://pake-oli.lampungselatankab.go.id/main/cekdata/' . $cekData;
        // $urlGen = base_url('main/cekdata/') . $cekData;
        $urlShort = $this->GenerateBitly($urlGen);


        $phone = $data_penduduk['no_telp'];
        if (substr($phone, 0, 2) != '62' and substr($phone, 0, 1) != '0') {
            $phone = '62' . $phone;
        } else if (substr($phone, 0, 1) == '0') {
            $phone = substr_replace($phone, "62", 0, 1);
        }



        $message = '';
        $message .= '_{(berisalam)},_ ' . "\n";
        $message .= $pembuka . 'Aplikasi *' . $nama_app . ' ' .  $nama_kab  . '*' . "\n";
        $message .= "\n";

        $message .= '*NIK :* ' . $data_penduduk['nik'] . "\n";
        $message .= '*Nomor KK :* ' . $data_penduduk['nik'] . "\n";
        $message .= '*Nama Lengkap :* ' . $data_penduduk['nama_lgkp'] . "\n";
        $message .= "\n";
        $message .= '*PIN Anda Adalah : ' . $data_penduduk['pin'] . '*' . "\n";

        $message .= "\n";

        $message .= "\n";
        $message .= 'Untuk melakukan *Verifikasi atau Lupa PIN* tersebut, Klik Link : ' . $urlShort . "\n";
        $message .= "\n";
        $message .= '_' . 'Ini adalah Pesan Otomatis, dan *tidak perlu di balas*, karena untuk saat ini *System kami tidak memonitoring Pesan/Panggilan Masuk* ke Whatsapp ini.' . '_' . "\n";
        $message .= "\n";
        $message .= "\n";
        $message .= 'Terimakasih,' . "\n";
        $message .= '*' . $nama_app . '*' . "\n";
        $message .= '*' . $nama_kab . '*' . "\n";

        $dataWhatsapp = [
            'no_reg' => $nik,
            'to_number' => $phone,
            'kriteria' => 'wa_notif_pemohon',
            // 'proses_id' => $id_proses_wa,
            'message' => $message
        ];
        $this->db->insert('wa_outbox', $dataWhatsapp);
        /// endwhatsapp


        $this->_mailClient();
        $getTbEmail = $this->db->get_where('outbox_email', ['no_reg' => $nik, 'is_sent' => 'U', 'kriteria' => 'email_validasi'])->row_array();

        $this->_client->addAddress($getTbEmail['email_to']); //email tujuan pengiriman email
        $this->_client->Subject = $getTbEmail['subject']; //subject email
        $this->_client->Body =  str_replace('{(berisalam)}', beri_salam(),  $getTbEmail['body']);
        if ($this->_client->send()) {
            $this->db->set('is_sent', 'Y');
            $this->db->set('date_sent', date("Y-m-d H:i:s"));
            $this->db->where('id_out_email', $getTbEmail['id_out_email']);
            $this->db->update('outbox_email');
            $this->_client->ClearAllRecipients();
            $this->_client->clearAttachments();
            return true;
        }
        return false;
    }

    public function GenerateBitly($url)
    {
        $dataUrl = $url;

        $dataPost = array(
            "url" => $url,
            "type" => 'splash'
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://s.hstg.my.id/api/url/add",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer tHNzZUBUFMWb",
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS => json_encode($dataPost),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data =  json_decode($response, true);
        $dataError = $data['error'];
        if ($dataError == 0)
            $dataUrl = $data['shorturl'];

        $dataUrl = preg_replace("#^[^:/.]*[:/]+#i", "", $dataUrl);
        return $dataUrl;
    }
    public function GenerateBitlyQR($url)
    {
        $dataUrl = $url;

        $dataPost = array(
            "url" => $url,
            "type" => 'splash'

        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://s.hstg.my.id/api/url/add",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer tHNzZUBUFMWb",
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS => json_encode($dataPost),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data =  json_decode($response, true);
        $dataError = $data['error'];
        if ($dataError == 0)
            $dataUrl = $data['shorturl'];

        // $dataUrl = preg_replace("#^[^:/.]*[:/]+#i", "", $dataUrl);
        return $dataUrl;
    }
    public function GenerateBitlyCustom($url)
    {
        $dataUrl = $url;

        $dataPost = array(
            "url" => $url,
            "type" => "3",
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://s.hstg.my.id/api/url/add",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer tHNzZUBUFMWb",
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS => json_encode($dataPost),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data =  json_decode($response, true);
        $dataError = $data['error'];
        if ($dataError == 0)
            $dataUrl = $data['short'];
        return $data;
    }
    private function GenerateBitly2($url)
    {
        $urlShort = $url;
        $long_url = $url;
        $apiv4 = 'https://api-ssl.bitly.com/v4/bitlinks';
        $genericAccessToken = 'c038a5576f31b4764029193b505b4340a9a3aa8a';

        $data = array(
            'long_url' => $long_url
        );
        $payload = json_encode($data);

        $header = array(
            'Authorization: Bearer ' . $genericAccessToken,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        );

        $ch = curl_init($apiv4);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        if ($result) {
            $ResJson = json_decode($result);
            if ($urlShort)
                $urlShort = $ResJson->link;
        }
        curl_close($ch);
        return $urlShort;
    }

    public function templateWhatsapp($no_reg)
    {
        $nama_app = $this->getIdWeb()['nama_aplikasi'];
        $nama_kab = $this->getIdWeb()['nama_kab'];
        $cekData = md5($no_reg);
        // $urlGen = 'https://pake-oli.lampungselatankab.go.id/main/cekdata/' . $cekData;
        $urlGen = base_url('main/cekdata/') . $cekData;
        $urlShort = $this->GenerateBitly($urlGen);
        // $urlShort = preg_replace( "#^[^:/.]*[:/]+#i", "", $urlShort);

        $this->db->select('nik
        , nama_lgkp
        , nama_layanan
        , jenis_alasan
        , proses
        , email
        , no_kk
        , no_telp
        , cttn_petugas
        , opsi_ambil
        , cetak_mandiri
        , f_tgl_lengkap(tgl_pengajuan) as tgl_pengajuan
        , DATE_FORMAT(tgl_pengajuan, "%H:%i:%s") as jam');
        $this->db->from('web_pemohon');
        $this->db->where('no_reg', $no_reg);
        $data_pemohon_wa = $this->db->get()->row_array();
        $phone = $data_pemohon_wa['no_telp'];
        if (substr($phone, 0, 2) != '62' and substr($phone, 0, 1) != '0') {
            $phone = '62' . $phone;
        } else if (substr($phone, 0, 1) == '0') {
            $phone = substr_replace($phone, "62", 0, 1);
        }

        $id_proses_wa = $data_pemohon_wa['proses'];
        $ketProses_wa = $this->db->get_where('web_proses', ['id' => $id_proses_wa])->row_array();
        $message = '';
        $message .= '_{(berisalam)},_ ' . "\n";
        $histori =  $this->getHistoriEmail($no_reg, $id_proses_wa);

        // if ($id_proses_wa == 10) {
        //     $histori =  $this->getHistoriWithRekamEmail($no_reg, $id_proses_wa);
        // } else {
        // }
        if ($data_pemohon_wa['proses'] == 1 or $data_pemohon_wa['proses'] == 9) {
            $pembuka = 'Terima Kasih atas kepercayaan Anda, telah menggunakan fasilitas ';
        } else {
            $pembuka = 'Kami telah melakukan proses Pengajuan Anda di ';
        }

        $message .= $pembuka . 'Aplikasi *' . $nama_app . ' ' .  $nama_kab  . '*' . "\n";
        $message .= "\n";
        if ($id_proses_wa == 1) {
            $message .= 'Berikut ringkasan Pengajuan yang telah kami terima :' . "\n";
        } else {
            $message .= 'Berikut ringkasan Pengajuan yang telah kami proses :' . "\n";
        }

        $message .= '*Nomor Registrasi : ' . $no_reg . '*' . "\n";
        $message .= '*NIK Pemohon :* ' . $data_pemohon_wa['nik'] . "\n";
        $message .= '*Nomor KK :* ' . $data_pemohon_wa['no_kk'] . "\n";
        $message .= '*Nama Pemohon :* ' . $data_pemohon_wa['nama_lgkp'] . "\n";
        $message .= "\n";
        $message .= '*Layanan :* ' . $data_pemohon_wa['nama_layanan'] . ' (' . $data_pemohon_wa['jenis_alasan'] . ')' . "\n";
        $message .= '*Waktu Pengajuan :* ' .  $data_pemohon_wa['tgl_pengajuan'] . ' *Jam :* ' . $data_pemohon_wa['jam'] . "\n";
        $message .= "\n";
        $message .= '*Status Proses: ' . $ketProses_wa['proses'] . '*' . "\n";
        if ($histori['tanggal'] != '' and $histori['jam'] != '' and $id_proses_wa != 9 and $id_proses_wa != 1) {
            $message .= '*Waktu Update :* ' .  $histori['tanggal'] . ' *Jam :* ' . $histori['jam'] . "\n";
        }
        $message .= '*Keterangan :* _' . $ketProses_wa['keterangan'] . '_' . "\n";
        $message .= "\n";
        if ($data_pemohon_wa['cttn_petugas'] != '' and ($id_proses_wa != 9 or $id_proses_wa != 1)) {
            $message .= '*Catatan Petugas :* ' . $data_pemohon_wa['cttn_petugas'] . "\n";
        }
        if ($id_proses_wa == 7) {
            $message .= '*Loket Pengambilan :* ' . $histori['loket_pengambilan'] . "\n";
        }
        if ($id_proses_wa == 15) {
            $dYanduk = $this->db->get_where('yanduk_satlantas', ['no_reg' => $no_reg])->row_array();
            $this->load->helper('tgl_indo');
            if ($dYanduk['tgl_kunjung_apr'])
                $tgl_apr = longdate_indo(date('Y-m-d', strtotime($dYanduk['tgl_kunjung_apr'])));
            else
                $tgl_apr = '-';

            $message .= '*Tanggal Kunjungan :* ' . $tgl_apr . "\n";
        }
        if ($id_proses_wa == 10) {
            $this->db->select('perekaman.*, f_tgl_lengkap(tgl_rekam) as tanggal_rekam');
            $this->db->from('perekaman');
            $this->db->where('no_reg', $no_reg);
            $data_rekam = $this->db->get()->row_array();
            $message .= '*Tanggal Perekaman :* ' . $data_rekam['tanggal_rekam'] . "\n";
            $message .= '*Loket Perekaman :* ' . $data_rekam['nama_loket'] . "\n";
            $message .= '*Nomor Antrian Perekaman :* ' . $data_rekam['urutan'] . "\n";
        }
        if ($id_proses_wa == 12) {
            if ($data_pemohon_wa['opsi_ambil'] == 1)
                if ($data_pemohon_wa['cetak_mandiri'] == 1) {
                    $mstrPDDK = $this->db->get_where('master_penduduk', ['nik' => $data_pemohon_wa['nik'], 'no_kk' => $data_pemohon_wa['no_kk']])->row_array();
                    $linkCtk = base_url('mandiri/cetak/') . encrypt_url($data_pemohon_wa['nik'] . $data_pemohon_wa['no_kk']) . '/' . encrypt_url($no_reg);
                    $linkCtkMdr = $this->GenerateBitly($linkCtk);
                    $message .= "\n";
                    $message .= 'Link Untuk Cetak Mandiri : *' . $linkCtkMdr . '*' . "\n";
                    $message .= 'PIN/Password Untuk Cetak Mandiri : *' . $mstrPDDK['pin'] . '*' . "\n";
                }
        }

        $message .= "\n";
        $message .= 'Untuk detail *Proses Pelayanan* tersebut, Klik Link : ' . $urlShort . "\n";
        $message .= "\n";
        $message .= '_' . 'Ini Pesan *Notifikasi Pelayanan PAKe-Oli*, ketik *MENU* untuk mengetahui Fitur Bot Lainnya dari Pelayanan Kami...' . '_' . "\n";
        $message .= "\n";
        $message .= "\n";
        $message .= 'Terimakasih,' . "\n";
        $message .= '*' . $nama_app . '*' . "\n";
        $message .= '*' . $nama_kab . '*' . "\n";

        $dataWhatsapp = [
            'no_reg' => $no_reg,
            'to_number' => $phone,
            'kriteria' => 'wa_notif_pemohon',
            'proses_id' => $id_proses_wa,
            'message' => $message
        ];
        $this->db->insert('wa_outbox', $dataWhatsapp);

        return true;
    }

    public function updateMaterPddk($post)
    {
        $nik = $post['nik'];
        $no_kk = $post['no_kk'];
        $fileName = $post['foto_swafoto'];

        $dataAwal = $this->db->get_where('master_penduduk', ['nik' => $nik, 'no_kk' => $no_kk])->row_array();
        $tokenAwal = $dataAwal['pin'];
        $verifiedAwal = $dataAwal['verified'];
        $email = $post['email'];
        $no_telp = $post['no_telp'];
        $tokenVer = $this->generateRandomString(6);
        if ($verifiedAwal == 0)
            $this->db->set('verified', 2);
        if ($tokenAwal == '' || $tokenAwal == null)
            $this->db->set('pin', $tokenVer);
        $this->db->set('email', $email);
        $this->db->set('no_telp', $no_telp);
        $this->db->where('nik', $nik);
        $this->db->where('no_kk', $no_kk);
        $this->db->update('master_penduduk');

        $this->copy_image_selfie($fileName, $nik, $no_kk);
        $this->db->affected_rows();

        $this->templateEmailVerifikasi($nik, $no_kk, false);
        // die;

        return true;
    }

    private function _mailClient()
    {
        $emailSetting = $this->db->get('email_setting')->row_array();
        $nama_app = $this->main_web->getIdWeb()['nama_aplikasi'];
        $this->_client = new PHPMailer(true);
        $this->_client->isSMTP();
        $this->_client->Host     = $emailSetting['host']; //sesuaikan sesuai nama domain hosting/server yang digunakan
        $this->_client->SMTPAuth = true;
        $this->_client->Username = $emailSetting['email']; // user email
        $this->_client->Password = $emailSetting['password']; // password email
        $this->_client->SMTPSecure = 'ssl';
        $this->_client->Port     = $emailSetting['port']; // Port SMTP
        $this->_client->setFrom($emailSetting['email'], $nama_app); // user email
        $this->_client->isHTML(true); // Set email format to HTML
        $this->_client->SMTPDebug = false;
    }

    private function sendMailVerification($nik)
    {

        $nama_app = $this->getIdWeb()['nama_aplikasi'];
        $this->_mailClient();
        $getDetailEmail = $this->db->get_where('outbox_email', ['no_reg' => $nik, 'is_sent' => 'U'])->row_array();

        $this->db->order_by('created', 'ASC');
        $getTbEmail = $this->db->get_where('outbox_email', ['is_sent' => 'N'], 10)->result_array();


        if ($getTbEmail) {
            for ($i = 0; $i < count($getTbEmail); $i++) {
                $id_proses = $getTbEmail[$i]['proses_id'];
                $ketProses = $this->db->get_where('web_proses', ['id' =>  $id_proses])->row_array();
                $email_subject = '[' . $nama_app . '] - Email Pemberitahuan ' . $ketProses['proses'];
                $this->_client->addAddress($getTbEmail[$i]['email_to']); //email tujuan pengiriman email
                $this->_client->Subject =  $email_subject; //subject email
                $this->_client->Body =  str_replace('{(berisalam)}', beri_salam(),  $getTbEmail[$i]['body']);
                if ($this->_client->send()) {
                    $this->db->set('is_sent', 'Y');
                    $this->db->set('date_sent', date("Y-m-d H:i:s"));
                    $this->db->where('id_out_email', $getTbEmail[$i]['id_out_email']);
                    $this->db->update('outbox_email');
                    $this->_client->ClearAllRecipients();
                    $this->_client->clearAttachments();
                }
            }
        }
    }

    private function generateRandomString($length = 10)
    {
        $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function InsertToMasterPenduduk($nik, $no_kk, $dataget)
    {
        $CekDataNik = $this->db->get_where('master_penduduk', ['nik' => $nik])->num_rows();
        if ($CekDataNik > 0) {
            $DataMasterUpdate = [
                'no_kk' => $no_kk,
                'nama_lgkp' => $dataget['name'],
                'no_kec' => $dataget['no_kec'],
                'no_kel' => $dataget['no_kel'],
                'tgl_lhr' => $dataget['tgl_lhr'],
                'no_rt' => $dataget['no_rt'],
                'no_rw' => $dataget['no_rw'],
                'alamat' => $dataget['alamat'],
                'kode_pos' => $dataget['kode_pos'],
                'aktif' => '1',
            ];
            $this->db->where('nik', $nik);
            $this->db->update('master_penduduk', $DataMasterUpdate);
        } else {
            $DataMasterInsert = [
                'nik' => $nik,
                'no_kk'  => $no_kk,
                'nama_lgkp'  => $dataget['name'],
                'no_kec'  => $dataget['no_kec'],
                'no_kel'  => $dataget['no_kel'],
                'tgl_lhr' => $dataget['tgl_lhr'],
                'no_rt' => $dataget['no_rt'],
                'no_rw' => $dataget['no_rw'],
                'alamat' => $dataget['alamat'],
                'kode_pos' => $dataget['kode_pos'],
                'aktif' => '1',
            ];
            $this->db->insert('master_penduduk', $DataMasterInsert);
        }

        return $this->db->get_where('master_penduduk', ['nik' => $nik])->row_array();
    }

    public function InsTbPemohon($post, $cekNIK)
    {

        if (null !== $this->session->userdata('user_uptd') && isset($post['user_uptd'])) {
            $user_uptd = htmlspecialchars($post['user_uptd'], true);
            $uptd = 1;
        } else {
            $user_uptd = null;
            $uptd = 0;
        }

        $kecamatan = htmlspecialchars($post['kecamatan_val'], true);
        $fcm_token = htmlspecialchars($post['fcm_token'], true);
        $desa = htmlspecialchars($post['desa_val'], true);
        $alasan = htmlspecialchars($post['alasan'], true);
        $no_telp = htmlspecialchars($post['no_telp'], true);
        $opsi_ambil = htmlspecialchars($post['opsi_ambil'], true);
        $id_alasan = substr($alasan, -6);
        $id_lay = substr($alasan, 0, 3);

        if (isset($cekNIK['name'])) {
            $no_kel = $cekNIK['no_kel'];
            $no_kec = $cekNIK['no_kec'];
            $nama_lengkap = strtoupper(htmlspecialchars($cekNIK['name'], true));
        } else {
            $no_kel = substr($desa, -4);
            $no_kec = substr($kecamatan, -2);
            $nama_lengkap = strtoupper(htmlspecialchars($post['nama_lgkp'], true));
        }


        $getDefaultWeb = $this->db->get('web_setting')->row_array();
        $getDefaultAdmin = $this->db->get('admin_setting')->row_array();

        $no_kab = $getDefaultWeb['no_kab'];
        $no_prop = $getDefaultWeb['no_prop'];


        $nama_layanan = $this->db->get_where('web_layanan', ['id_lay' => $id_lay])->row_array()['nama_layanan'];
        $form_tambahan = $this->db->get_where('web_layanan', ['id_lay' => $id_lay])->row_array()['form_tambahan'];
        $jenis_alasan = $this->db->get_where('web_alasan_layanan', ['id_alasan' => $id_alasan, 'id_lay' => $id_lay])->row_array()['detail_alasan'];

        if (substr($no_telp, 0, 2) != '62' and substr($no_telp, 0, 1) != '0') {
            $no_telp = '62' . $no_telp;
        } else if (substr($no_telp, 0, 1) == '0') {
            $no_telp = substr_replace($no_telp, "62", 0, 1);
        }

        //$no_reg = $this->NewgetNoregSeqn($kecamatan,$id_lay, $id_alasan);
        $no_reg = $this->getNoregSeqn($kecamatan);

        if ($form_tambahan == 'perekaman') {
            if ($getDefaultAdmin['verifikasi_perekaman'] == 'Y') {
                $proses = 9;
            } else {
                $proses = 10;
            }
        } else if ($form_tambahan == 'yanduk') {
            $proses = 14;
        } else
            $proses = 1;

        if ($no_reg != '') {
            if ($post['file_name_uploads']) {
                $this->copy_image($post, $no_reg, $id_alasan, $id_lay);
            }
            $dataIns = [
                'nik' => htmlspecialchars($post['nik'], true),
                'no_kk' => htmlspecialchars($post['no_kk'], true),
                'nama_lgkp' => $nama_lengkap,
                'email' => htmlspecialchars($post['email'], true),
                'no_telp' => $no_telp,
                'fcm_token' => $fcm_token,
                'cttn_pemohon' => htmlspecialchars($post['cttn_pemohon'], true),
                'no_kel' => $no_kel,
                'no_kec' => $no_kec,
                'no_kab' => $no_kab,
                'no_prop' => $no_prop,
                'id_alasan' => $id_alasan,
                'user_uptd' => $user_uptd,
                'uptd' => $uptd,
                'id_lay' => $id_lay,
                'no_reg' => $no_reg,
                'proses' => $proses,
                'opsi_ambil' => $opsi_ambil,
                'jenis_alasan' => $jenis_alasan,
                'nama_layanan' => $nama_layanan,
                'form_tambahan' => $form_tambahan
            ];

            $this->db->insert('web_pemohon', $dataIns);
            $insCek = $this->db->affected_rows();
            // die;

            if ($insCek > 0) {

                // membuat qrCodegenQRCodeToken($no_reg)
                //   $this->genQRCode($no_reg, htmlspecialchars($post['nik'], true));
                $this->genQRCodeToken($no_reg);
                // membuat Noreg Untuk Cek
                //$sessionNoreg = [$no_reg];
                $this->session->set_userdata('noRegSukses', $no_reg);
                //$this->session->set_userdata('noRegSukses', $no_reg);
                $this->session->set_userdata('NikSukses', htmlspecialchars($post['nik'], true));


                // email Pemohon
                // $dataEmail = [
                //     'no_reg' => $no_reg,
                //     'email_to' => htmlspecialchars($post['email']),
                //     'kriteria' => 'email_notif_pemohon',
                //     'proses_id' => $proses
                // ];
                // $this->db->insert('outbox_email', $dataEmail);



                //Insert Form jika ADA
                if ($form_tambahan != '') {
                    if ($form_tambahan == 'akta_lahir') {
                        $this->akta_lahir($post, $no_reg);
                    } else if ($form_tambahan == 'perekaman') {
                        $this->perekaman($post, $no_reg, $proses);
                    } else if ($form_tambahan == 'pindah_datang') {
                        $this->pindah_datang($post, $no_reg, $no_kec, $no_kel);
                    } else if ($form_tambahan == 'akta_kematian') {
                        $this->akta_kematian($post, $no_reg);
                    } else if ($form_tambahan == 'kia') {
                        $this->kia($post, $no_reg);
                    } else if ($form_tambahan == 'akta_cerai') {
                        $this->akta_cerai($post, $no_reg);
                    } else if ($form_tambahan == 'akta_kawin') {
                        $this->akta_kawin($post, $no_reg);
                    } else if ($form_tambahan == 'yanduk') {
                        $this->yanduk_satlantas($post, $no_reg);
                    }
                }


                if ($opsi_ambil > 1) {
                    $this->InsOpsiAmbil($no_reg, $opsi_ambil, $post);
                }

                $this->templateEmail($no_reg);
                $WahstappUse = $getDefaultWeb['wa_api'];
                if ($WahstappUse == 'Y') {
                    $this->templateWhatsapp($no_reg);
                }

                // INTERGRASI MAJU LAMSEL
                $this->load->library('layanan_citigov');
                $token = encrypt_url(time());
                $alamat = '(' . $no_prop . ') ' . $this->getNamaProp($no_prop);
                $alamat .= ';(' . $no_kab . ') ' . $this->getNamaKab($no_prop, $no_kab);
                $alamat .= ';(' . $no_kec . ') ' . $this->getNamaKec($no_prop, $no_kab, $no_kec);
                $alamat .= ';(' . $no_kel . ') ' . $this->getNamaKel($no_prop, $no_kab, $no_kec, $no_kel);
                // (18) LAMPUNG;(1)LAMPUNG SELATAN;(4)NATAR;(2001)KUCING 

                $payload = [
                    'nama_aplikasi'  => 'Pake-Oli Lampung Selatan',
                    'nama_layanan'   => $nama_layanan . ' - ' . $jenis_alasan,
                    'id_layanan'     => $id_lay,
                    'nomor_tiket'    => $no_reg,
                    'status'         => 0,
                    'nama_pemohon'   => $nama_lengkap,
                    'nik_pemohon'    => htmlspecialchars($post['nik'], true),
                    'email_pemohon'  => htmlspecialchars($post['email'], true),
                    'alamat_pemohon' =>  $alamat,
                    'telepon_pemohon' => $no_telp,
                    'nip_petugas'    => '198204122010011014',
                    'nama_petugas'   => 'PAULUS CHRISTIAN SUBIANTO',
                    'bidang_petugas' => 'Pengelolaan Informasi Administrasi Kependudukan',
                    'jabatan_petugas' => 'Kepala Bidang',
                    'tgl_pengajuan' => date('Y-m-d H:i:s')
                ];

                $result = $this->layanan_citigov->buat_layanan($payload, $token);
                // print_r($result);
                return true;
            } else {
                return false;
            }
        }
    }


    public function InsOpsiAmbil($no_reg, $opsi_ambil, $data_post)
    {
        $dataInsert = array();
        $dataInsert['no_reg'] = $no_reg;
        $dataInsert['nama_penerima'] = strtoupper($data_post['nama_penerima']);
        $dataInsert['no_kec_kirim'] = $data_post['no_kec_kirim'];
        $dataInsert['no_kel_kirim'] = $data_post['no_kel_kirim'];
        $dataInsert['no_rt_kirim'] = $data_post['no_rt_kirim'];
        $dataInsert['no_rw_kirim'] = $data_post['no_rw_kirim'];
        $dataInsert['alamat_lgkp_kirim']  = $data_post['alamat_lgkp_kirim'];
        $dataInsert['no_telp_lain']  = $data_post['no_telp_lain'];
        $dataInsert['kode_pos']  = $data_post['kode_pos'];
        $dataInsert['alamat_pelengkap_antar']  = $data_post['alamat_pelengkap_antar'];

        if ($opsi_ambil == 2) {
            $this->db->insert('pos_indonesia', $dataInsert);
            $insCek = $this->db->affected_rows();
        }
        return $insCek;
    }

    public function EditTbPemohon($post, $no_reg)
    {
        //populate edit
        //key no_reg
        //post email
        //post no_telp
        //post cttn_pemohon
        //post form tambahan
        //
        $no_telp = htmlspecialchars($post['no_telp'], true);
        $nik = htmlspecialchars($post['nik'], true);
        //
        if (substr($no_telp, 0, 2) != '62' and substr($no_telp, 0, 1) != '0') {
            $no_telp = '62' . $no_telp;
        } else if (substr($no_telp, 0, 1) == '0') {
            $no_telp = substr_replace($no_telp, "62", 0, 1);
        }
        //
        $email = htmlspecialchars($post['email'], true);
        $cttn_pemohon = htmlspecialchars($post['cttn_pemohon'], true);
        $alasan = htmlspecialchars($post['alasan'], true);
        $id_alasan = substr($alasan, -6);
        $id_lay = substr($alasan, 0, 3);
        //
        $where = [
            'no_reg' => $no_reg,
            'nik' => $nik,
            'id_lay' => $id_lay,
            'id_alasan' => $id_alasan
        ];
        //
        $dataAwal =  $this->db->get_where('web_pemohon', $where)->row_array();
        //
        if ($dataAwal) {
            //
            $no_kel = $dataAwal['no_kel'];
            $no_kec = $dataAwal['no_kec'];
            $no_kab = $dataAwal['no_kab'];
            $no_prop = $dataAwal['no_prop'];
            $fcm_token = htmlspecialchars($post['fcm_token'], true);
            $form_tambahan = $this->db->get_where('web_layanan', ['id_lay' => $dataAwal['id_lay']])->row_array()['form_tambahan'];

            $jenis_alasan = $dataAwal['jenis_alasan'];
            //
            $dataUpdate = [
                'email' => htmlspecialchars($post['email'], true),
                'no_telp' => $no_telp,
                'fcm_token' => $fcm_token,
                'cttn_pemohon' => htmlspecialchars($post['cttn_pemohon'], true),
                'proses' => '5',
                'cttn_petugas' => '',
            ];

            return true;
        } else {
            // if false
            return false;
        }

        $kecamatan = htmlspecialchars($post['kecamatan_val'], true);
        $desa = htmlspecialchars($post['desa_val'], true);
        $no_telp = htmlspecialchars($post['no_telp'], true);
        $id_alasan = substr($alasan, -6);
        $id_lay = substr($alasan, 0, 3);
        $no_kel = substr($desa, -4);
        $no_kec = substr($kecamatan, -2);
        $no_kab = substr($kecamatan, 2, 2);
        $no_prop = substr($kecamatan, 0, 2);
        $nama_layanan = $this->db->get_where('web_layanan', ['id_lay' => $id_lay])->row_array()['nama_layanan'];
        $form_tambahan = $this->db->get_where('web_layanan', ['id_lay' => $id_lay])->row_array()['form_tambahan'];
        $jenis_alasan = $this->db->get_where('web_alasan_layanan', ['id_alasan' => $id_alasan, 'id_lay' => $id_lay])->row_array()['detail_alasan'];



        if (substr($no_telp, 0, 2) != '62' and substr($no_telp, 0, 1) != '0') {
            $no_telp = '62' . $no_telp;
        } else if (substr($no_telp, 0, 1) == '0') {
            $no_telp = substr_replace($no_telp, "62", 0, 1);
        }

        $no_reg = $this->getNoregSeqn($kecamatan);

        if ($form_tambahan == 'perekaman') {
            if ($this->db->get('admin_setting')->row_array()['verifikasi_perekaman'] == 'Y') {
                $proses = 9;
            } else {
                $proses = 10;
            }
        } else {
            $proses = 1;
        }

        if ($no_reg != '') {
            if ($post['file_name_uploads']) {
                $this->copy_image($post, $no_reg, $id_alasan, $id_lay);
            }
            $dataIns = [
                'nik' => htmlspecialchars($post['nik'], true),
                'no_kk' => htmlspecialchars($post['no_kk'], true),
                'nama_lgkp' => strtoupper(htmlspecialchars($post['nama_lgkp'], true)),
                'email' => htmlspecialchars($post['email'], true),
                'no_telp' => $no_telp,
                // 'fcm_token' => $fcm_token,
                'cttn_pemohon' => htmlspecialchars($post['cttn_pemohon'], true),
                'no_kel' => $no_kel,
                'no_kec' => $no_kec,
                'no_kab' => $no_kab,
                'no_prop' => $no_prop,
                'id_alasan' => $id_alasan,
                'id_lay' => $id_lay,
                'no_reg' => $no_reg,
                'proses' => $proses,
                'jenis_alasan' => $jenis_alasan,
                'nama_layanan' => $nama_layanan,
                'form_tambahan' => $form_tambahan,
            ];
            $this->db->insert('web_pemohon', $dataIns);
            $insCek = $this->db->affected_rows();
            if ($insCek > 0) {
                // membuat qrCodegenQRCodeToken($no_reg)

                $this->genQRCodeToken($no_reg);
                // membuat Noreg Untuk Cek
                $this->session->set_userdata('noRegSukses', $no_reg);
                $this->session->set_userdata('NikSukses', htmlspecialchars($post['nik'], true));

                // email Pemohon
                $dataEmail = [
                    'no_reg' => $no_reg,
                    'email_to' => htmlspecialchars($post['email']),
                    'kriteria' => 'email_notif_pemohon',
                ];

                $this->db->insert('outbox_email', $dataEmail);

                $WahstappUse = $this->db->get('web_setting')->row_array()['wa_api'];
                if ($WahstappUse == 'Y') {
                    // WhatsApp Pemohon
                    $dataWhatsapp = [
                        'no_reg' => $no_reg,
                        'to_number' => $no_telp,
                        'kriteria' => 'wa_notif_pemohon',
                    ];
                    $this->db->insert('wa_outbox', $dataWhatsapp);
                }

                //Insert Form jika ADA
                if ($form_tambahan != '') {
                    if ($form_tambahan == 'akta_lahir') {
                        $this->akta_lahir($post, $no_reg);
                    } else if ($form_tambahan == 'perekaman') {
                        $this->perekaman($post, $no_reg, $proses);
                    } else if ($form_tambahan == 'pindah_datang') {
                        $this->pindah_datang($post, $no_reg, $no_kec, $no_kel);
                    } else if ($form_tambahan == 'akta_kematian') {
                        $this->akta_kematian($post, $no_reg);
                    } else if ($form_tambahan == 'kia') {
                        $this->kia($post, $no_reg);
                    } else if ($form_tambahan == 'akta_cerai') {
                        $this->akta_cerai($post, $no_reg);
                    } else if ($form_tambahan == 'akta_kawin') {
                        $this->akta_kawin($post, $no_reg);
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }



    public function updateKuota()
    {
        /*** Start Update Kuota ***/
        $dataLoket = $this->db->get('web_tanggal_rekam')->result_array();
        for ($i = 0; $i < count($dataLoket); $i++) {
            $id_loket =  $dataLoket[$i]['id_loket'];
            $id_tgl_rekam =  $dataLoket[$i]['id_tgl_rekam'];
            $terisi = $this->db->get_where('perekaman', ['id_loket' => $id_loket, 'id_tgl_rekam' => $id_tgl_rekam, 'approve' => 'Y'])->num_rows();
            $kuota = $dataLoket[$i]['kuota'];

            $sisa = $kuota - $terisi;

            if ($sisa < 0) {
                $sisa = 0;
            }

            $this->db->set('sisa', $sisa);
            $this->db->where('id_loket', $id_loket);
            $this->db->where('id_tgl_rekam', $id_tgl_rekam);
            $this->db->update('web_tanggal_rekam');
        }

        /*** END Update Kuota ***/
    }
    public function genQRCode($no_reg)
    {
        if (!is_dir('uploads/' . $no_reg)) {
            mkdir('./uploads/' . $no_reg, 0777, TRUE);
        }

        $this->load->library('ciqrcode'); //pemanggilan library QR CODE

        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']     = './assets/'; //string, the default is application/cache/
        $config['errorlog']     = './assets/'; //string, the default is application/logs/
        $config['imagedir']     = './uploads/' . $no_reg . '/'; //direktori penyimpanan qr code
        $config['quality']      = true; //boolean, the default is true
        $config['size']         = '1024'; //interger, the default is 1024
        $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)

        $this->ciqrcode->initialize($config);

        $image_name = 'QrCode_Cekdata.png'; //buat name dari qr code sesuai dengan nim
        $token = md5($no_reg);
        $params['data'] = base_url('main/cekdata/') . $token; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
    }

    public function genQRCodeToken($no_reg)
    {
        if (!is_dir('uploads/' . $no_reg)) {
            mkdir('./uploads/' . $no_reg, 0777, TRUE);
        }

        $this->load->library('ciqrcode'); //pemanggilan library QR CODE

        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']     = './assets/'; //string, the default is application/cache/
        $config['errorlog']     = './assets/'; //string, the default is application/logs/
        $config['imagedir']     = './uploads/' . $no_reg . '/'; //direktori penyimpanan qr code
        $config['quality']      = true; //boolean, the default is true
        $config['size']         = '1024'; //interger, the default is 1024
        $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)

        $this->ciqrcode->initialize($config);
        $token = md5($no_reg);
        $dataUrlCek = $this->GenerateBitlyQR(base_url('main/cekdata/') . $token);

        $image_name = 'QrCode_Cekdata.png'; //buat name dari qr code sesuai dengan nim
        $params['data'] = $dataUrlCek; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE


        $image_name = 'QrCode_TTD.png'; //buat name dari qr code sesuai dengan nim
        $dataUrlVer = $this->GenerateBitlyQR(base_url('main/verifikasi/') . $token);
        $params['data'] = $dataUrlVer; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
    }

    public function perekaman($post, $no_reg, $proses)
    {
        $id_loket = htmlspecialchars($post['id_loket'], true);
        $id_tgl_rekam = htmlspecialchars($post['id_tgl_rekam'], true);
        $tgl_rekam = $this->db->get_where('web_tanggal_rekam', ['id_loket' => $id_loket, 'id_tgl_rekam' => $id_tgl_rekam])->row_array()['tanggal'];
        $nama_loket = $this->db->get_where('web_loket_rekam', ['id_loket' => $id_loket])->row_array()['nama_loket'];

        if ($proses == 10) {
            $approve =  'Y';
            $rowNum = $this->db->get_where('perekaman', ['id_loket' => $id_loket, 'id_tgl_rekam' => $id_tgl_rekam, 'approve' => 'Y'])->num_rows();
            $urutan = $rowNum + 1;
        } else {
            $approve =  'N';
            $urutan = null;
        }

        $dataPerekaman = [
            'no_reg' => $no_reg,
            'id_loket' => $id_loket,
            'nama_loket' => $nama_loket,
            'id_tgl_rekam' => $id_tgl_rekam,
            'tgl_rekam' => $tgl_rekam,
            'approve' => $approve,
            'urutan' => $urutan
        ];


        $this->db->insert('perekaman', $dataPerekaman);
        $insCek = $this->db->affected_rows();

        if ($insCek > 0) {
            $this->updateKuota();
        }
    }

    public function getNamaProp($no_prop)
    {
        $data = $this->db->get_where('setup_prop', ['no_prop' => $no_prop])->row_array();
        return 'Provinsi ' . $data['nama_prop'];
    }

    public function getNamaKab($no_prop, $no_kab)
    {
        $data = $this->db->get_where('setup_kab', ['no_prop' => $no_prop, 'no_kab' => $no_kab])->row_array();
        $nama_kab_def = $data['nama_kab'];
        if (substr($data['no_kab'], 0, 1) != '7') {
            $nama_kab_def = 'Kabupaten ' . $nama_kab_def;
        } else if (substr($data['no_kab'], 0, 1) == '7') {
            $nama_kab_def = 'Kota ' . $nama_kab_def;
        }
        return $nama_kab_def;
    }

    public function getNamaKec($no_prop, $no_kab, $no_kec)
    {
        $data = $this->db->get_where('setup_kec', ['no_prop' => $no_prop, 'no_kab' => $no_kab, 'no_kec' => $no_kec])->row_array();
        return 'Kecamatan ' . $data['nama_kec'];
    }
    public function getOnlyNamaKec($no_prop, $no_kab, $no_kec)
    {
        $data = $this->db->get_where('setup_kec', ['no_prop' => $no_prop, 'no_kab' => $no_kab, 'no_kec' => $no_kec])->row_array();
        return $data['nama_kec'];
    }

    public function getNamaKel($no_prop, $no_kab, $no_kec, $no_kel)
    {
        $data = $this->db->get_where('setup_kel', ['no_prop' => $no_prop, 'no_kab' => $no_kab, 'no_kec' => $no_kec, 'no_kel' => $no_kel])->row_array();
        $nama_kel = $data['nama_kel'];
        if (substr($data['no_kel'], 0, 1) == '1') {
            $nama_kel = 'Kelurahan ' . $nama_kel;
        } else if (substr($data['no_kel'], 0, 1) == '2') {
            $nama_kel = 'Desa ' . $nama_kel;
        }
        return $nama_kel;
    }

    public function getOnlyNamaKel($no_prop, $no_kab, $no_kec, $no_kel)
    {
        $data = $this->db->get_where('setup_kel', ['no_prop' => $no_prop, 'no_kab' => $no_kab, 'no_kec' => $no_kec, 'no_kel' => $no_kel])->row_array();
        $nama_kel = $data['nama_kel'];

        return $nama_kel;
    }

    public function pindah_datang($post, $no_reg, $no_kec, $no_kel)
    {

        $jenis_mutasi = htmlspecialchars($post['jenis_mutasi'], true);

        if ($jenis_mutasi == 'pindah') {
            $prop_tujuan = htmlspecialchars($post['prop_tujuan'], true);
            $kab_tujuan = htmlspecialchars($post['kab_tujuan'], true);
            $kec_tujuan = htmlspecialchars($post['kec_tujuan'], true);
            $desa_tujuan = htmlspecialchars($post['desa_tujuan'], true);
            $alamat_pindah = htmlspecialchars($post['alamat_pindah'], true);
            $no_rt_pindah = htmlspecialchars($post['no_rt_pindah'], true);
            $no_rw_pindah = htmlspecialchars($post['no_rw_pindah'], true);
            $jumlah_pengikut = htmlspecialchars($post['jumlah_pengikut'], true);
            if ($jumlah_pengikut > 1 and  $jumlah_pengikut < 7) {
                $nik_pengikut = htmlspecialchars($post['nik_pengikut'], true);
            } else {
                $nik_pengikut = null;
            }

            $id_alasan_pindah = htmlspecialchars($post['alasan_pindah'], true);
            $alasan_pindah = $this->db->get_where('alasan_pindah', ['id_alasan_pindah' => $id_alasan_pindah])->row_array()['alasan_pindah'];

            $no_kab_o = substr($kab_tujuan, -2);
            $no_kec_o = substr($kec_tujuan, -2);
            $no_kel_o = substr($desa_tujuan, -4);

            $nama_prop = $this->getNamaProp($prop_tujuan);
            $nama_kab = $this->getNamaKab($prop_tujuan, $no_kab_o);
            $nama_kec = $this->getNamaKec($prop_tujuan, $no_kab_o, $no_kec_o);
            $nama_kel = $this->getNamaKel($prop_tujuan, $no_kab_o, $no_kec_o, $no_kel_o);

            $dataInsPindah = [
                'no_reg' => $no_reg,
                'jenis_mutasi' => $jenis_mutasi,
                'alasan_pindah' => $alasan_pindah,
                'prop_tujuan' => $prop_tujuan,
                'nama_prop_tujuan' => $nama_prop,
                'kab_tujuan' => $no_kab_o,
                'nama_kab_tujuan' => $nama_kab,
                'kec_tujuan' => $no_kec_o,
                'nama_kec_tujuan' => $nama_kec,
                'desa_tujuan' => $no_kel_o,
                'nama_desa_tujuan' => $nama_kel,
                'alamat_pindah' => $alamat_pindah,
                'no_rt_pindah' => $no_rt_pindah,
                'no_rw_pindah' => $no_rw_pindah,
                'jumlah_pengikut' => $jumlah_pengikut,
                'nik_pengikut' => $nik_pengikut,
            ];
            $this->db->insert('pindah_datang', $dataInsPindah);
        }
        if ($jenis_mutasi == 'datang') {
            $no_skpwni = htmlspecialchars($post['no_skpwni'], true);
            $no_rt_datang = htmlspecialchars($post['no_rt_datang'], true);
            $no_rw_datang = htmlspecialchars($post['no_rw_datang'], true);
            $alamat_datang = htmlspecialchars($post['no_rw_datang'], true);
            $prop_datang = $this->db->get('web_setting')->row_array()['no_prop'];
            $kab_datang = $this->db->get('web_setting')->row_array()['no_kab'];
            $kec_datang = $no_kec;
            $kel_datang = $no_kel;

            $nama_prop = $this->getNamaProp($prop_datang);
            $nama_kab = $this->getNamaKab($prop_datang, $kab_datang);
            $nama_kec = $this->getNamaKec($prop_datang, $kab_datang, $kec_datang);
            $nama_kel = $this->getNamaKel($prop_datang, $kab_datang, $kec_datang, $kel_datang);

            $dataInsDatang = [
                'no_reg' => $no_reg,
                'jenis_mutasi' => $jenis_mutasi,
                'no_skpwni' => $no_skpwni,
                'no_rt_datang' => $no_rt_datang,
                'no_rw_datang' => $no_rw_datang,
                'prop_datang' => $prop_datang,
                'nama_prop_datang' => $nama_prop,
                'kab_datang' => $kab_datang,
                'nama_kab_datang' => $nama_kab,
                'kec_datang' => $kec_datang,
                'nama_kec_datang' => $nama_kec,
                'kel_datang' => $kel_datang,
                'nama_kel_datang' => $nama_kel,
                'alamat_datang' => $alamat_datang,
            ];
            $this->db->insert('pindah_datang', $dataInsDatang);
        }
    }

    function UpdateKuotaRekam($id_loket, $id_tgl_rekam)
    {
        $terisi = $this->db->get_where('perekaman', ['id_loket' => $id_loket, 'id_tgl_rekam' => $id_tgl_rekam, 'approve' => 'Y'])->num_rows();
        $kuota = $this->db->get_where('web_tanggal_rekam', ['id_loket' => $id_loket, 'id_tgl_rekam' => $id_tgl_rekam])->row_array()['kuota'];
        $sisa = $kuota - $terisi;

        if ($sisa < 0) {
            $sisa = 0;
        }

        $this->db->set('sisa', $sisa);
        $this->db->where('id_loket', $id_loket);
        $this->db->where('id_tgl_rekam', $id_tgl_rekam);
        $this->db->update('web_tanggal_rekam');
    }

    public function yanduk_satlantas($post, $no_reg)
    {
        $id_hub = htmlspecialchars($post['hub_pemohon'], true);
        $hub_pemohon = $this->db->get_where('web_hub_pemohon', ['id_hub' => $id_hub])->row_array()['hubungan'];
        $tgl_kunjung_req = htmlspecialchars($post['tgl_kunjungan'], true);
        $kontak_lain = htmlspecialchars($post['kontak_lain'], true);
        $dataYanduk = [
            'no_reg' => $no_reg,
            'id_hub' => $id_hub,
            'kontak_lain' => $kontak_lain,
            'hub_pemohon' => $hub_pemohon,
            'tgl_kunjung_req' => $tgl_kunjung_req,
        ];
        $this->db->insert('yanduk_satlantas', $dataYanduk);
    }
    public function akta_kematian($post, $no_reg)
    {
        $id_hub = htmlspecialchars($post['hub_pemohon'], true);
        $id_sebab_mati = htmlspecialchars($post['sebab_kematian'], true);
        $hub_pemohon = $this->db->get_where('web_hub_pemohon', ['id_hub' => $id_hub])->row_array()['hubungan'];
        $sebab_mati = $this->db->get_where('sebab_mati', ['id_sebab_mati' => $id_sebab_mati])->row_array()['sebab_mati'];

        $dataAktaMati = [
            'no_reg' => $no_reg,
            'hub_pemohon' => $hub_pemohon,
            'id_hub' => $id_hub,
            'id_sebab_mati' => $id_sebab_mati,
            'nik_jenazah' => htmlspecialchars($post['nik_jenazah'], true),
            'no_kk_jenazah' => htmlspecialchars($post['no_kk_jenazah'], true),
            'nama_jenazah' => strtoupper(htmlspecialchars($post['nama_jenazah'], true)),
            'tempat_lahir_jenazah' => strtoupper(htmlspecialchars($post['tempat_lahir_jenazah'], true)),
            'tempat_kematian' => strtoupper(htmlspecialchars($post['tempat_kematian'], true)),
            'tgl_lahir_jenazah' => htmlspecialchars($post['tgl_lahir_jenazah'], true),
            'tgl_mati_jenazah' => htmlspecialchars($post['tgl_mati_jenazah'], true),
            'anak_ke' => htmlspecialchars($post['anak_ke'], true),
            'nama_ayah' => strtoupper(htmlspecialchars($post['nama_ayah'], true)),
            'nama_ibu' => strtoupper(htmlspecialchars($post['nama_ibu'], true)),
            'sebab_kematian' => $sebab_mati,
        ];

        $this->db->insert('akta_mati', $dataAktaMati);
    }
    public function kia($post, $no_reg)
    {
        $id_hub = htmlspecialchars($post['hub_pemohon'], true);
        $hub_pemohon = $this->db->get_where('web_hub_pemohon', ['id_hub' => $id_hub])->row_array()['hubungan'];

        $dataKIA = [
            'no_reg' => $no_reg,
            'hub_pemohon' => $hub_pemohon,
            'id_hub' => $id_hub,
            'nik_anak' => htmlspecialchars($post['nik_anak'], true),
            'no_akta_lhr' => htmlspecialchars($post['no_akta_lhr'], true),
            'no_kk_anak' => htmlspecialchars($post['no_kk_anak'], true),
            'nama_anak' => strtoupper(htmlspecialchars($post['nama_anak'], true)),
            'tempat_lahir' => strtoupper(htmlspecialchars($post['tempat_lahir'], true)),
            'tgl_lahir' => htmlspecialchars($post['tgl_lahir'], true),
            'nama_ayah' => strtoupper(htmlspecialchars($post['nama_ayah'], true)),
            'nama_ibu' => strtoupper(htmlspecialchars($post['nama_ibu'], true)),
            'anak_ke' => strtoupper(htmlspecialchars($post['anak_ke'], true)),
        ];

        $this->db->insert('kia', $dataKIA);
    }

    public function akta_cerai($post, $no_reg)
    {

        $ListPengaju_cerai = [
            '1' => 'Suami',
            '2' => 'Istri',
        ];

        $ListSebabCerai = [
            '1' => 'Berbuat Zina',
            '2' => 'Pemabuk/Pemadat',
            '3' => 'Penjudi',
            '4' => 'Meninggalkan Pasangan > 2 Tahun Tanpa Alasan',
            '5' => 'Hukuman Penjara Diatas 5 Tahun/Lebih Berat',
            '6' => 'Melakukan KDRT',
            '7' => 'Mendapat Cacat Badan/Penyakit',
            '8' => 'Perselisihan/Pertengkaran',
            '9' => 'Lainnya',
        ];

        $pengaju_cerai = $ListPengaju_cerai[htmlspecialchars($post['pengaju_cerai'], true)];
        $sebab_cerai = $ListSebabCerai[htmlspecialchars($post['sebab_cerai'], true)];

        $dataAktaCerai = [
            'no_reg' => $no_reg,
            'pengaju_cerai' => $pengaju_cerai,
            'nomor_putusan' => trim(strtoupper(htmlspecialchars($post['nomor_putusan'], true))),
            'tgl_cerai' => htmlspecialchars($post['tgl_cerai'], true),
            'no_kk_cerai' => htmlspecialchars($post['no_kk_cerai'], true),
            'sebab_cerai' => $sebab_cerai,
            'no_akta_kawin_cerai' => strtoupper(htmlspecialchars($post['no_akta_kawin_cerai'], true)),
            'tgl_kawin_cerai' => htmlspecialchars($post['tgl_kawin_cerai'], true),
            'nik_suami' => htmlspecialchars($post['nik_suami'], true),
            'nama_suami' => strtoupper(htmlspecialchars($post['nama_suami'], true)),
            'tempat_lahir_suami' => strtoupper(htmlspecialchars($post['tempat_lahir_suami'], true)),
            'tgl_lahir_suami' => htmlspecialchars($post['tgl_lahir_suami'], true),
            'nik_ayah_suami' => htmlspecialchars($post['nik_ayah_suami'], true),
            'nama_ayah_suami' => strtoupper(htmlspecialchars($post['nama_ayah_suami'], true)),
            'nik_ibu_suami' => htmlspecialchars($post['nik_ibu_suami'], true),
            'nama_ibu_suami' => strtoupper(htmlspecialchars($post['nama_ibu_suami'], true)),
            'cerai_suami_ke' => htmlspecialchars($post['cerai_suami_ke'], true),
            'nik_istri' => htmlspecialchars($post['nik_istri'], true),
            'nama_istri' => strtoupper(htmlspecialchars($post['nama_istri'], true)),
            'tempat_lahir_istri' => strtoupper(htmlspecialchars($post['tempat_lahir_istri'], true)),
            'tgl_lahir_istri' => htmlspecialchars($post['tgl_lahir_istri'], true),
            'nik_ayah_istri' => htmlspecialchars($post['nik_ayah_istri'], true),
            'nama_ayah_istri' => strtoupper(htmlspecialchars($post['nama_ayah_istri'], true)),
            'nik_ibu_istri' => htmlspecialchars($post['nik_ibu_istri'], true),
            'nama_ibu_istri' => strtoupper(htmlspecialchars($post['nama_ibu_istri'], true)),
            'cerai_istri_ke' => htmlspecialchars($post['cerai_istri_ke'], true)
        ];
        $this->db->insert('akta_cerai', $dataAktaCerai);
    }

    public function akta_kawin($post, $no_reg)
    {
        $LisAgama = [
            '1' => 'Kristen',
            '2' => 'Katholik',
            '3' => 'Hindu',
            '4' => 'Buddha',
            '5' => 'Khonghucu',
        ];
        $agama_kawin = $LisAgama[trim(htmlspecialchars($post['agama_kawin'], true))];
        $dataAktaKawin = [
            'no_reg' => $no_reg,
            'agama_kawin' => $agama_kawin,
            'tgl_kawin' => htmlspecialchars($post['tgl_kawin'], true),
            'tempat_kawin' => strtoupper(htmlspecialchars($post['tempat_kawin'], true)),
            'nama_pemuka' => strtoupper(htmlspecialchars($post['nama_pemuka'], true)),
            'nik_suami' => htmlspecialchars($post['nik_suami'], true),
            'no_kk_suami' => htmlspecialchars($post['no_kk_suami'], true),
            'nama_suami' => strtoupper(htmlspecialchars($post['nama_suami'], true)),
            'tempat_lahir_suami' => strtoupper(htmlspecialchars($post['tempat_lahir_suami'], true)),
            'tgl_lahir_suami' => htmlspecialchars($post['tgl_lahir_suami'], true),
            'nik_ayah_suami' => htmlspecialchars($post['nik_ayah_suami'], true),
            'nama_ayah_suami' => strtoupper(htmlspecialchars($post['nama_ayah_suami'], true)),
            'nik_ibu_suami' => htmlspecialchars($post['nik_ibu_suami'], true),
            'nama_ibu_suami' => strtoupper(htmlspecialchars($post['nama_ibu_suami'], true)),
            'kawin_suami_ke' => htmlspecialchars($post['kawin_suami_ke'], true),
            'anak_ke_suami' => htmlspecialchars($post['anak_ke_suami'], true),
            'nik_istri' => htmlspecialchars($post['nik_istri'], true),
            'no_kk_istri' => htmlspecialchars($post['no_kk_istri'], true),
            'nama_istri' => strtoupper(htmlspecialchars($post['nama_istri'], true)),
            'tempat_lahir_istri' => strtoupper(htmlspecialchars($post['tempat_lahir_istri'], true)),
            'tgl_lahir_istri' => htmlspecialchars($post['tgl_lahir_istri'], true),
            'nik_ayah_istri' => htmlspecialchars($post['nik_ayah_istri'], true),
            'nama_ayah_istri' => strtoupper(htmlspecialchars($post['nama_ayah_istri'], true)),
            'nik_ibu_istri' => htmlspecialchars($post['nik_ibu_istri'], true),
            'nama_ibu_istri' => strtoupper(htmlspecialchars($post['nama_ibu_istri'], true)),
            'kawin_istri_ke' => htmlspecialchars($post['kawin_istri_ke'], true),
            'anak_ke_istri' => htmlspecialchars($post['anak_ke_istri'], true),
        ];
        $this->db->insert('akta_kawin', $dataAktaKawin);
    }
    public function akta_lahir($post, $no_reg)
    {

        $id_hub = htmlspecialchars($post['hub_pemohon'], true);
        $id_jenis_lahir = htmlspecialchars($post['jenis_lahir'], true);
        $hub_pemohon = $this->db->get_where('web_hub_pemohon', ['id_hub' => $id_hub])->row_array()['hubungan'];
        $jenis_lahir = $this->db->get_where('web_jenis_lahir', ['id_jenis_lahir' => $id_jenis_lahir])->row_array()['jenis_kelahiran'];

        $dataAktaLahir = [
            'no_reg' => $no_reg,
            'hub_pemohon' => $hub_pemohon,
            'id_hub' => $id_hub,
            'id_jenis_lahir' => $id_jenis_lahir,
            'nik_anak' => htmlspecialchars($post['nik_anak'], true),
            'no_kk_anak' => htmlspecialchars($post['no_kk_anak'], true),
            'nama_anak' => strtoupper(htmlspecialchars($post['nama_anak'], true)),
            'tempat_lahir' => strtoupper(htmlspecialchars($post['tempat_lahir'], true)),
            'tgl_lahir' => htmlspecialchars($post['tgl_lahir'], true),
            'nama_ayah' => strtoupper(htmlspecialchars($post['nama_ayah'], true)),
            'nama_ibu' => strtoupper(htmlspecialchars($post['nama_ibu'], true)),
            'anak_ke' => strtoupper(htmlspecialchars($post['anak_ke'], true)),
            'jenis_lahir' => $jenis_lahir,
        ];

        $this->db->insert('akta_lahir', $dataAktaLahir);
    }

    public function getProp()
    {
        $this->db->select('*');
        $this->db->from('setup_prop');
        $this->db->order_by('no_prop', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getPersyaratan()
    {
        $this->db->select('*');
        $this->db->from('web_persyaratan');
        $this->db->where('active', 'Y');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getSyaratKetentuan()
    {
        $this->db->select('*');
        $this->db->from('syarat_ketentuan');
        $data = $this->db->get()->row_array();
        return $data;
    }

    public function getBantuan()
    {
        $this->db->select('*');
        $this->db->from('web_bantuan');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getPejabat()
    {
        $this->db->select('*');
        $this->db->from('web_pejabat');
        $this->db->where('active', 'Y');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getAllPejabat()
    {
        $this->db->select('*');
        $this->db->from('web_pejabat');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }
    public function getGallery()
    {
        $this->db->select('*');
        $this->db->from('galeri_gambar');
        $this->db->where('active', 'Y');

        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }
    public function getSlider()
    {
        $data = array();
        $this->db->select('galeri_gambar.file,galeri_gambar.deskripsi, "gallery" as file_dest');
        $this->db->from('galeri_gambar');
        $this->db->where('galeri_gambar.active', 'Y');
        $this->db->where('galeri_gambar.slider', 'Y');

        $data1 = $this->db->get()->result_array();

        $this->db->select('galeri_penghargaan.file,galeri_penghargaan.deskripsi ,"gallery" as file_dest');
        $this->db->from('galeri_penghargaan');
        $this->db->where('galeri_penghargaan.active', 'Y');
        $this->db->where('galeri_penghargaan.slider', 'Y');
        $data2 = $this->db->get()->result_array();

        $this->db->select('web_berita.gambar as file,web_berita.judul as deskripsi , "berita" as file_dest, web_berita.id as id_berita');
        $this->db->from('web_berita');
        $this->db->where('web_berita.active', '1');
        $this->db->where('web_berita.gambar !=', 'default.png');

        $data3 = $this->db->get()->result_array();

        $data = array_merge($data1, $data3, $data2);


        return $data;
    }

    public function getRating()
    {
        $this->db->select('(sum(is_rate)/count(*)) as avg , count(*) as total_row');
        $this->db->from('web_pemohon');
        $this->db->where('is_rate>', 0);
        // $this->db->group_by('is_rate');

        $Master = $this->db->get()->row_array();
        $data['star'] = round($Master['avg']);
        $data['value'] = round($Master['avg'], 1);
        $total_row = $Master['total_row'];
        $data['resp'] = $total_row;

        // $data['ttl_data'] = $this->db->get()->row_array()['ttol'];


        $this->db->select('is_rate,  count(is_rate) cnt');
        $this->db->from('web_pemohon');
        $this->db->where('is_rate>', 0);
        $this->db->group_by('is_rate');
        $detail = $this->db->get()->result_array();

        $data['detail'] = [
            5 => ['width' => 0],
            4 => ['width' => 0],
            3 => ['width' => 0],
            2 => ['width' => 0],
            1 => ['width' => 0],
        ];

        foreach ($detail as $key => $ca) {
            for ($i = 5; $i > 0; $i--) {
                if ($i == $ca['is_rate']) {
                    $data['detail'][$i]['width'] = round(($ca['cnt'] / $total_row) * 100);
                    // $data['detail'][$i]['cnt'] = $ca['cnt'];
                }
            }
        }




        return $data;
    }
    public function getPenghargaan()
    {
        $this->db->select('*');
        $this->db->from('galeri_penghargaan');
        $this->db->where('active', 'Y');

        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getLink()
    {
        $this->db->select('*');
        $this->db->from('link_terkait');
        $this->db->order_by('urutan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function getNoregSeqn($kecamatan)
    {
        $this->load->helper('date');
        $date =  date("d-m-Y");

        $c = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan, 'tanggal' => $date])->num_rows();
        if ($c < 1) {
            $data = array(
                'kode_awal' => $kecamatan,
                'hitung' => 1,
                'tanggal' => $date,
            );
            $this->db->insert('seqn_noreg', $data);
            $generate_noreg = 1;
        } else {
            $before = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan, 'tanggal' => $date])->row_array();
            $this->db->set('hitung', $before['hitung'] + 1);
            $this->db->where('kode_awal', $kecamatan);
            $this->db->where('tanggal', $date);
            $this->db->update('seqn_noreg');
            $after = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan, 'tanggal' => $date])->row_array();
            $generate_noreg = $after['hitung'];
        }
        $strA = $kecamatan;
        if ($generate_noreg % 2 == 0) {
            $strB = mdate('%y%d%m%H%i%s', time());
        } else {
            $strB = mdate('%y%m%d%H%s%i', time());
        }

        $strC = sprintf('%05d', $generate_noreg);
        $noReg =  $strA . '-' . $strB . '-' . $strC;
        $this->session->set_userdata('no_reg', $noReg);

        return $noReg;
    }

    public function NewgetNoregSeqn($kecamatan, $id_lay, $id_alasan)
    {
        $c = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan, 'id_lay' => $id_lay, 'id_alasan' => $id_alasan])->num_rows();
        if ($c < 1) {
            $data = array(
                'kode_awal' => $kecamatan,
                'id_lay' => $id_lay,
                'id_alasan' => $id_alasan,
                'hitung' => 1
            );
            $this->db->insert('seqn_noreg', $data);
            $generate_noreg = 1;
        } else {
            $before = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan])->row_array();
            $this->db->set('hitung', $before['hitung'] + 1);
            $this->db->where('kode_awal', $kecamatan);
            $this->db->update('seqn_noreg');
            $after = $this->db->get_where('seqn_noreg', ['kode_awal' => $kecamatan])->row_array();
            $generate_noreg = $after['hitung'];
        }
        $strA = $kecamatan;
        if ($generate_noreg % 2 == 0) {
            $strB = mdate('%y%d%m%H%i%s', time());
        } else {
            $strB = mdate('%y%m%d%H%s%i', time());
        }

        $strC = sprintf('%05d', $generate_noreg);
        $noReg =  $strA . '-' . $strB . '-' . $strC;
        $this->session->set_userdata('no_reg', $noReg);

        return $noReg;
    }

    function copy_image($post, $no_reg, $id_alasan, $id_lay)
    {
        if (!is_dir('uploads/' . $no_reg)) {
            mkdir('./uploads/' . $no_reg, 0777, TRUE);
        }

        foreach ($post['file_name_uploads'] as $keyid_File => $isiFile) {
            $filenames = $isiFile[0];
            $fileToCopy = FCPATH . 'uploads/temp_images/' . $filenames;
            $fileToCopyThumbs = FCPATH . 'uploads/temp_images/thumbnail_' . $filenames;
            $destinationOfCopy = FCPATH . 'uploads/' . $no_reg . '/' . $filenames;
            $destinationOfCopyThumbs = FCPATH . 'uploads/' . $no_reg . '/thumbnail_' . $filenames;
            $success = copy($fileToCopy, $destinationOfCopy);
            if ($success) {
                $successThumb = copy($fileToCopyThumbs, $destinationOfCopyThumbs);
                if ($successThumb) {
                    unlink($fileToCopyThumbs);
                }
                $nama_syarat = $this->db->get_where('web_detail_syarat', ['id_syarat' => $keyid_File])->row_array()['nama_syarat'];
                $data = array(
                    'no_reg' => $no_reg,
                    'file' => $filenames,
                    'id_syarat' => $keyid_File,
                    'nama_syarat' => $nama_syarat,
                    'id_alasan' => $id_alasan,
                    'id_lay' => $id_lay
                );
                $this->db->insert('web_upload_syarat', $data);
                unlink($fileToCopy);
            }
        }
    }
    function copy_image_selfie($filenames, $nik, $no_kk)
    {
        if (!is_dir('uploads/swafoto_pddk/' . $nik)) {
            mkdir('./uploads/swafoto_pddk/' . $nik, 0777, TRUE);
        }

        // $filenames = $filenam;
        $fileToCopy = FCPATH . 'uploads/temp_images/' . $filenames;
        $fileToCopyThumbs = FCPATH . 'uploads/temp_images/thumbnail_' . $filenames;
        $destinationOfCopy = FCPATH . 'uploads/swafoto_pddk/' . $nik . '/' . $filenames;
        $destinationOfCopyThumbs = FCPATH . 'uploads/swafoto_pddk/' . $nik . '/thumbnail_' . $filenames;
        $success = copy($fileToCopy, $destinationOfCopy);
        if ($success) {
            $successThumb = copy($fileToCopyThumbs, $destinationOfCopyThumbs);
            if ($successThumb) {
                unlink($fileToCopyThumbs);
                $this->db->set('swafoto', $filenames);
                $this->db->where('nik', $nik);
                $this->db->where('no_kk', $no_kk);
                $this->db->update('master_penduduk');
            }
            unlink($fileToCopy);
        }
    }

    function upload_image($post, $no_reg, $id_alasan, $id_lay)
    {
        if (!is_dir('uploads/' . $no_reg)) {
            mkdir('./uploads/' . $no_reg, 0777, TRUE);
        }

        $config['max_size'] = '5000';
        $config['upload_path'] = './uploads/' . $no_reg; //path folder
        $config['allowed_types'] = 'jpg|png|jpeg'; //type yang dapat diakses bisa anda sesuaikan
        $config['encrypt_name'] = TRUE; //nama yang terupload nantinya
        $this->load->library('upload', $config);

        $this->db->select('web_detail_syarat.id_syarat, web_detail_syarat.nama_syarat');
        $this->db->from('web_detail_syarat');
        $this->db->join('web_data_syarat', 'web_detail_syarat.id_syarat = web_data_syarat.id_syarat');
        $this->db->where('web_data_syarat.id_lay', $id_lay);
        $this->db->where('web_data_syarat.id_alasan', $id_alasan);

        $data1 = $this->db->get()->result_array();

        foreach ($data1 as $key) {
            if (!empty($_FILES[$key['id_syarat']]['name'])) {
                if (!$this->upload->do_upload($key['id_syarat'])) {
                } else {
                    $gbr = $this->upload->data();

                    $path = FCPATH . 'uploads/' . $no_reg . '/';
                    $filename = $gbr['file_name'];
                    $file_size = $gbr['file_size'];

                    $this->resize_image($file_size, $gbr, $path, $filename);

                    $data = array(
                        'no_reg' => $no_reg,
                        'file' => $gbr['file_name'],
                        'id_syarat' => $key['id_syarat'],
                        'nama_syarat' => $key['nama_syarat'],
                        'id_alasan' => $id_alasan,
                        'id_lay' => $id_lay
                    );
                    $this->db->insert('web_upload_syarat', $data);
                }
            }
        }
    }



    public function resize_image($file_size, $image_data, $path, $filename)
    {
        $this->load->library('image_lib');
        $w = $image_data['image_width']; // original image's width
        $h = $image_data['image_height']; // original images's height

        if ($file_size > 1000) {

            $n_w = round($w / 2); // destination image's width
            $n_h = round($h / 2); // destination image's height
        } else {
            $n_w = $w; // destination image's width
            $n_h = $h; // destination image's height
        }

        $source_ratio = $w / $h;
        $new_ratio = $n_w / $n_h;
        if ($source_ratio != $new_ratio) {

            $config[0]['image_library'] = 'gd2';
            $config[0]['source_image'] = $path . $filename;
            $config[0]['maintain_ratio'] = FALSE;
            if ($new_ratio > $source_ratio || (($new_ratio == 1) && ($source_ratio < 1))) {
                $config[0]['width'] = $w;
                $config[0]['height'] = round($w / $new_ratio);
                $config[0]['y_axis'] = round(($h - $config[0]['height']) / 2);
                $config[0]['x_axis'] = 0;
            } else {
                $config[0]['width'] = round($h * $new_ratio);
                $config[0]['height'] = $h;
                $size_config['x_axis'] = round(($w - $config[0]['width']) / 2);
                $size_config['y_axis'] = 0;
            }

            $this->image_lib->initialize($config);
            $this->image_lib->crop();
            $this->image_lib->clear();
        }

        $config[0]['image_library'] = 'gd2';
        $config[0]['source_image'] = $path . $filename;
        $config[0]['new_image'] = $path . $filename;
        $config[0]['maintain_ratio'] = TRUE;
        $config[0]['width'] = $n_w;
        $config[0]['height'] = $n_h;

        $config[1] = array(
            'image_library' => 'gd2',
            'source_image' => $path . $filename,
            'new_image' => $path . 'thumbnail_' . $filename,
            'maintain_ratio' => FALSE,
            'width' => 500,
            'height' => 333,
            'quality' => '50%',
        );

        foreach ($config as $item) {
            $this->image_lib->initialize($item);
            if (!$this->image_lib->resize()) {
                return false;
            }
            $this->image_lib->clear();
        }
    }


    function getCekDataKK($no_kk)
    {
        $this->db->select('no_reg
        , web_pemohon.proses
        , nama_lgkp, nik, no_kk
        , web_pemohon.id_lay,web_pemohon.proses
        , cttn_petugas
        , web_pemohon.proses
        , web_proses.proses as ket_proses
        , web_pemohon.id_alasan
        , web_pemohon.nama_layanan
        , web_pemohon.jenis_alasan
        , web_pemohon.is_rate
        , f_tgl_lengkap(tgl_pengajuan) as tgl_pengajuan');
        $this->db->from('web_pemohon');
        $this->db->join('web_layanan', 'web_pemohon.id_lay = web_layanan.id_lay');
        $this->db->join('web_proses', 'web_pemohon.proses = web_proses.id');
        $this->db->where('no_kk', $no_kk);
        $this->db->order_by('web_pemohon.tgl_pengajuan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function getCekData($no_Reg)
    {
        $this->db->select('no_reg
        , nama_lgkp, nik, no_kk
        , web_pemohon.id_lay,web_pemohon.proses
        , cttn_petugas
        , web_pemohon.proses
        , web_proses.proses as ket_proses
        , web_pemohon.id_alasan
        , web_pemohon.nama_layanan
        , web_pemohon.jenis_alasan
        , web_pemohon.is_rate
        , f_tgl_lengkap(tgl_pengajuan) as tgl_pengajuan');
        $this->db->from('web_pemohon');
        $this->db->join('web_layanan', 'web_pemohon.id_lay = web_layanan.id_lay');
        $this->db->join('web_proses', 'web_pemohon.proses = web_proses.id');
        $this->db->where('no_reg', $no_Reg);
        $this->db->order_by('web_pemohon.tgl_pengajuan', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function getCekDataPrint($no_Reg)
    {
        $this->db->select('no_reg
        , nama_lgkp, nik, no_kk
        , web_pemohon.id_lay,web_pemohon.proses
        , cttn_petugas
        , web_pemohon.proses
        , web_pemohon.cetak_mandiri
        , web_pemohon.opsi_ambil
        , web_proses.proses as ket_proses
        , web_pemohon.id_alasan
        , web_pemohon.nama_layanan
        , web_pemohon.jenis_alasan
        , f_tgl_lengkap(tgl_pengajuan) as tgl_pengajuan');
        $this->db->from('web_pemohon');
        $this->db->join('web_layanan', 'web_pemohon.id_lay = web_layanan.id_lay');
        $this->db->join('web_proses', 'web_pemohon.proses = web_proses.id');
        $this->db->where('no_reg', $no_Reg);
        $data = $this->db->get()->row_array();
        return $data;
    }
    function getHistori($no_Reg)
    {
        $this->db->select('web_histori_proses.no_reg, id_proses, updated_by, catatan, loket_pengambilan, web_proses.proses, web_proses.keterangan, DATE_FORMAT(date(tanggal), "%d-%m-%Y") as tanggal, DATE_FORMAT(tanggal, "%H:%i:%s") as jam');
        $this->db->from('web_histori_proses');
        $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
        //  $this->db->join('perekaman', 'web_histori_proses.no_reg = perekaman.no_reg');
        $this->db->where_in('web_histori_proses.no_reg', $no_Reg);
        $this->db->order_by('web_histori_proses.tanggal', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function getHistoriEmail($no_Reg, $id_proses)
    {
        $this->db->select('
          web_histori_proses.no_reg
        , id_proses
        , updated_by
        , catatan
        , loket_pengambilan
        , web_proses.proses
        , web_proses.keterangan
        , DATE_FORMAT(date(tanggal), "%d-%m-%Y") as tanggal
        , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');
        $this->db->from('web_histori_proses');
        $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
        $this->db->where_in('web_histori_proses.no_reg', $no_Reg);
        $this->db->where_in('web_histori_proses.id_proses', $id_proses);
        $this->db->order_by('web_histori_proses.tanggal', 'ASC');
        $data = $this->db->get()->row_array();
        return $data;
    }

    function getHistoriKK($no_Reg)
    {
        $this->db->select('web_histori_proses.no_reg
        , id_proses
        , updated_by
        , catatan
        , loket_pengambilan
        , web_proses.proses
        , web_proses.keterangan
        , DATE_FORMAT(date(tanggal), "%d-%m-%Y") as tanggal
        , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');
        $this->db->from('web_histori_proses');
        $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
        $this->db->where_in('web_histori_proses.no_reg', $no_Reg);
        $this->db->order_by('web_histori_proses.tanggal', 'ASC');
        $data = $this->db->get()->row_array();
        return $data;
    }

    function getHistoriWithRekam($no_Reg)
    {
        $this->db->select('
          id_proses
        , web_histori_proses.no_reg
        , f_tgl_lengkap(perekaman.tgl_rekam) as tgl_rekam
        , perekaman.nama_loket as nama_loket
        , perekaman.urutan as urutan_loket
        , updated_by
        , catatan
        , loket_pengambilan
        , web_proses.proses
        , web_proses.keterangan
        , date_format(date(tanggal), "%d-%m-%Y") as tanggal
        , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');

        $this->db->from('web_histori_proses');
        $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
        $this->db->join('perekaman', 'web_histori_proses.no_reg = perekaman.no_reg');
        $this->db->where('web_histori_proses.no_reg', $no_Reg);
        $this->db->order_by('web_histori_proses.tanggal', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function getHistoriWithRekamEmail($no_Reg, $id_proses)
    {

        $this->db->select('id_proses
        , web_histori_proses.no_reg
        , f_tgl_lengkap(perekaman.tgl_rekam) as tgl_rekam
        , perekaman.nama_loket as nama_loket
        , perekaman.urutan as urutan_loket
        , updated_by
        , catatan
        , loket_pengambilan
        , web_proses.proses
        , web_proses.keterangan
        , date_format(date(tanggal), "%d-%m-%Y") as tanggal
        , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');

        $this->db->from('web_histori_proses');
        $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
        $this->db->join('perekaman', 'web_histori_proses.no_reg = perekaman.no_reg');
        $this->db->where('web_histori_proses.no_reg', $no_Reg);
        $this->db->where('web_histori_proses.id_proses', $id_proses);
        $this->db->order_by('web_histori_proses.tanggal', 'ASC');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function getHistrKK($no_Reg, $proses)
    {
        if ($proses == '10') {
            $this->db->select('id_proses
            , web_histori_proses.no_reg
            , f_tgl_lengkap(perekaman.tgl_rekam) as tgl_rekam
            , perekaman.nama_loket as nama_loket
            , perekaman.urutan as urutan_loket
            , updated_by
            , catatan
            , loket_pengambilan
            , web_proses.proses
            , web_proses.keterangan
            , date_format(date(tanggal), "%d-%m-%Y") as tanggal
            , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');

            $this->db->from('web_histori_proses');
            $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
            $this->db->join('perekaman', 'web_histori_proses.no_reg = perekaman.no_reg');
            $this->db->where('web_histori_proses.no_reg', $no_Reg);
            $this->db->order_by('web_histori_proses.tanggal', 'ASC');
            $data = $this->db->get()->row_array();
        } else {
            $this->db->select('web_histori_proses.no_reg
            , id_proses
            , updated_by
            , catatan
            , loket_pengambilan
            , web_proses.proses
            , web_proses.keterangan
            , DATE_FORMAT(date(tanggal), "%d-%m-%Y") as tanggal
            , DATE_FORMAT(tanggal, "%H:%i:%s") as jam');
            $this->db->from('web_histori_proses');
            $this->db->join('web_proses', 'web_proses.id = web_histori_proses.id_proses');
            //  $this->db->join('perekaman', 'web_histori_proses.no_reg = perekaman.no_reg');
            $this->db->where_in('web_histori_proses.no_reg', $no_Reg);
            $this->db->order_by('web_histori_proses.tanggal', 'ASC');
            $data = $this->db->get()->row_array();
        }

        return $data;
    }

    public function getDashboard2()
    {
        $this->load->helper('date');
        $this->db->select('*');
        $this->db->from('web_layanan');
        $this->db->order_by('id_lay', 'ASC');
        $getDataPel = $this->db->get()->result_array();

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cBelum[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where proses not in (3,10,7,8,11)';
            $layBelum = $this->db->query($sql)->result_array();
            //$this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay'], 'proses<' => '10'])->result_array();
            if (isset($lay['id_lay']) == $getDataPel[$i]['id_lay']) {
                $cBelum[$getDataPel[$i]['nama_layanan']]++;
            }
            foreach ($layBelum as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cBelum[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cTolak[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where proses in (3)';
            $layTolak = $this->db->query($sql)->result_array();
            //$this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay'], 'proses<' => '10'])->result_array();
            if (isset($lay['id_lay']) == $getDataPel[$i]['id_lay']) {
                $cTolak[$getDataPel[$i]['nama_layanan']]++;
            }
            foreach ($layTolak as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cTolak[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cSudah[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where proses in (7,8,10,11)';
            $laySudah = $this->db->query($sql)->result_array();

            foreach ($laySudah as $keyHar) {
                if ($keyHar['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cSudah[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        $dataDash = array();
        $tbLayanan = $this->db->get('web_pemohon')->num_rows();
        if ($tbLayanan > 0) {
            foreach ($getDataPel as $Key) {
                if ($cBelum[$Key['nama_layanan']] > 0 or $cSudah[$Key['nama_layanan']] > 0) {
                    $dataDash[] = array(
                        'tanggal' => mdate("%Y-%m-%d", time()),
                        'nama_layanan' => $Key['nama_layanan'],
                        'id_lay' => $Key['id_lay'],
                        'belum' => $cBelum[$Key['nama_layanan']],
                        'tolak' => $cTolak[$Key['nama_layanan']],
                        'sudah' => $cSudah[$Key['nama_layanan']],
                        'total' => $cSudah[$Key['nama_layanan']] + $cBelum[$Key['nama_layanan']] + $cTolak[$Key['nama_layanan']],
                    );
                }
            }
        } else {
            $dataDash = array();
        }
        return $dataDash;
    }
    public function getNewDashboard()
    {
        $this->load->helper('date');
        $this->db->select('web_layanan.*, web_group_layanan.link_group');
        $this->db->from('web_layanan');
        $this->db->join('web_group_layanan', 'web_group_layanan.id_group = web_layanan.id_group');
        $this->db->order_by('id_lay', 'ASC');
        $getDataPel = $this->db->get()->result_array();

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cSemua[$getDataPel[$i]['nama_layanan']] = 0;
            $laySem = $this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay']])->result_array();

            // if (isset($laySem['id_lay']) == $getDataPel[$i]['id_lay']) {
            //     $cSemua[$getDataPel[$i]['nama_layanan']]++;
            // }
            foreach ($laySem as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cSemua[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }


        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHarian[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = CURDATE()';
            $layHar = $this->db->query($sql)->result_array();
            foreach ($layHar as $keyHar) {
                if ($keyHar['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHarian[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin1[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 1 DAY)';
            $layHarMin1 = $this->db->query($sql)->result_array();
            foreach ($layHarMin1 as $keyHarMin1) {
                if ($keyHarMin1['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin1[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin2[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 2 DAY)';
            $layHarMin2 = $this->db->query($sql)->result_array();
            foreach ($layHarMin2 as $keyHarMin2) {
                if ($keyHarMin2['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin2[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin3[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 3 DAY)';
            $layHarMin3 = $this->db->query($sql)->result_array();
            foreach ($layHarMin3 as $keyHarMin3) {
                if ($keyHarMin3['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin3[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin4[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 4 DAY)';
            $layHarMin4 = $this->db->query($sql)->result_array();
            foreach ($layHarMin4 as $keyHarMin4) {
                if ($keyHarMin4['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin4[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin5[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 5 DAY)';
            $layHarMin5 = $this->db->query($sql)->result_array();
            foreach ($layHarMin5 as $keyHarMin5) {
                if ($keyHarMin5['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin5[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin6[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 6 DAY)';
            $layHarMin6 = $this->db->query($sql)->result_array();
            foreach ($layHarMin6 as $keyHarMin6) {
                if ($keyHarMin6['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin6[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHmin7[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = date(CURDATE() - INTERVAL 6 DAY)';
            $layHarMin7 = $this->db->query($sql)->result_array();
            foreach ($layHarMin7 as $keyHarMin7) {
                if ($keyHarMin7['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHmin7[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cSelesai[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where proses in (7,8,10,11,12,13,15)';
            $layHar = $this->db->query($sql)->result_array();
            foreach ($layHar as $keyHar) {
                if ($keyHar['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cSelesai[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cBelum[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where proses in (1,2,4,5,6,9,14)';
            $layBelum = $this->db->query($sql)->result_array();
            //$this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay'], 'proses<' => '10'])->result_array();
            if (isset($lay['id_lay']) == $getDataPel[$i]['id_lay']) {
                $cBelum[$getDataPel[$i]['nama_layanan']]++;
            }
            foreach ($layBelum as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cBelum[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }


        for ($i = 0; $i < count($getDataPel); $i++) {
            $cTolak[$getDataPel[$i]['nama_layanan']] = 0;
            $sql = 'select * from web_pemohon where proses in (3)';
            $layTolak = $this->db->query($sql)->result_array();
            //$this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay'], 'proses<' => '10'])->result_array();
            if (isset($lay['id_lay']) == $getDataPel[$i]['id_lay']) {
                $cTolak[$getDataPel[$i]['nama_layanan']]++;
            }
            foreach ($layTolak as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cTolak[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }

        $sqlTtlHar = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = CURDATE()';
        $totalHarian = $this->db->query($sqlTtlHar)->num_rows();

        $sqlTtlSelesai = 'select * from web_pemohon where proses in (7,8,10,11,12,13,15)';
        $totalSelesai = $this->db->query($sqlTtlSelesai)->num_rows();



        $sqlTtlTolak = 'select * from web_pemohon where proses in (3)';
        $totalTolak = $this->db->query($sqlTtlTolak)->num_rows();

        $sqlTtlBlm = 'select * from web_pemohon where proses not in (1,2,4,5,6,9,14)';
        $totalBelum = $this->db->query($sqlTtlBlm)->num_rows();

        $sqlTtlSelesai2 = 'select * from web_pemohon where proses in (7,8,10,11,12,13,15)';
        $totalSelesai2 = $this->db->query($sqlTtlSelesai2)->num_rows();

        $sqlTtlBlm2 = 'select * from web_pemohon where proses not in (7,8,10,11,12,13,15)';
        $totalBelum2 = $this->db->query($sqlTtlBlm2)->num_rows();

        $sqlTtlSemua = 'select * from web_pemohon';
        $totalSemua = $this->db->query($sqlTtlSemua)->num_rows();

        $tbLayanan = $this->db->get('web_pemohon')->num_rows();
        if ($tbLayanan > 0) {
            foreach ($getDataPel as $Key) {
                if ($cSemua[$Key['nama_layanan']] > 0) {
                    $dataDash['detail'][] = array(
                        'id_lay' => $Key['id_lay'],
                        'nama_layanan' => $Key['nama_layanan'],
                        'link' => $Key['link'],
                        'link_group' => $Key['link_group'],
                        'nama_singkat' => $Key['nama_singkat'],
                        'warna' => $Key['warna'],
                        'icon' => $Key['icon'],
                        'id_lay' => $Key['id_lay'],
                        'harian' => $cHarian[$Key['nama_layanan']],
                        'h_min1' => $cHmin1[$Key['nama_layanan']],
                        'h_min2' => $cHmin2[$Key['nama_layanan']],
                        'h_min3' => $cHmin3[$Key['nama_layanan']],
                        'h_min4' => $cHmin4[$Key['nama_layanan']],
                        'h_min5' => $cHmin5[$Key['nama_layanan']],
                        'h_min6' => $cHmin6[$Key['nama_layanan']],
                        'h_min7' => $cHmin7[$Key['nama_layanan']],
                        'belum' => $cBelum[$Key['nama_layanan']],
                        'tolak' => $cTolak[$Key['nama_layanan']],
                        'selesai' => $cSelesai[$Key['nama_layanan']],
                        'p_selesai' => round(($cSelesai[$Key['nama_layanan']] / $cSemua[$Key['nama_layanan']]) * 100),
                        'p_reject' => round(($cTolak[$Key['nama_layanan']] / $cSemua[$Key['nama_layanan']]) * 100),
                        'p_belum' => 100 - (round(($cTolak[$Key['nama_layanan']] / $cSemua[$Key['nama_layanan']]) * 100) + round(($cSelesai[$Key['nama_layanan']] / $cSemua[$Key['nama_layanan']]) * 100)),
                        'semua' => $cSemua[$Key['nama_layanan']],
                    );
                }
            }


            $dataDash['summary'][] = [
                'total_proses' => $totalBelum,
                'total_proses2' => $totalBelum2,
                'total_tolak' => $totalTolak,
                'total_selesai2' => $totalSelesai2,
                'total_selesai' => $totalSelesai,
                'total_harian' => $totalHarian,
                'total_semua' => $totalSemua,
                'p_ttl_belum' => round(($totalBelum / $totalSemua) * 100),
                'p_ttl_belum2' => round(($totalBelum2 / $totalSemua) * 100),
                'p_ttl_tolak' => round(($totalTolak / $totalSemua) * 100),
                'p_ttl_selesai' => round(($totalSelesai / $totalSemua) * 100),
                'p_ttl_selesai2' => round(($totalSelesai2 / $totalSemua) * 100),
            ];
        } else {
            $dataDash = array();
        }

        return $dataDash;
    }

    public function getDashboard()
    {
        $this->load->helper('date');
        $this->db->select('*');
        $this->db->from('web_layanan');
        $this->db->order_by('id_lay', 'ASC');
        $getDataPel = $this->db->get()->result_array();

        for ($i = 0; $i < count($getDataPel); $i++) {
            $cSemua[$getDataPel[$i]['nama_layanan']] = 0;
            $laySem = $this->db->get_where('web_pemohon', ['id_lay' => $getDataPel[$i]['id_lay']])->result_array();

            if (isset($lay['id_lay']) == $getDataPel[$i]['id_lay']) {
                $cSemua[$getDataPel[$i]['nama_layanan']]++;
            }
            foreach ($laySem as $keySem) {
                if ($keySem['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cSemua[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }


        for ($i = 0; $i < count($getDataPel); $i++) {
            $cHarian[$getDataPel[$i]['nama_layanan']] = 0;

            $sql = 'select * from web_pemohon where date(web_pemohon.tgl_pengajuan) = CURDATE()';
            $layHar = $this->db->query($sql)->result_array();
            foreach ($layHar as $keyHar) {
                if ($keyHar['id_lay'] == $getDataPel[$i]['id_lay']) {
                    $cHarian[$getDataPel[$i]['nama_layanan']]++;
                }
            }
        }
        $tbLayanan = $this->db->get('web_pemohon')->num_rows();
        if ($tbLayanan > 0) {
            foreach ($getDataPel as $Key) {
                if ($cSemua[$Key['nama_layanan']] > 0) {
                    $dataDash[] = array(
                        'tanggal' => mdate("%Y-%m-%d", time()),
                        'nama_layanan' => $Key['nama_layanan'],
                        'id_lay' => $Key['id_lay'],
                        'semua' =>    $cSemua[$Key['nama_layanan']],
                        'harian' => $cHarian[$Key['nama_layanan']]
                    );
                }
            }
        } else {
            $dataDash = array();
        }

        return $dataDash;
    }
}
