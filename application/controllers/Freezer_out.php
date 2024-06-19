<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
class Freezer_out extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        is_login();
        $this->load->model('Freezer_out_model');
        $this->load->library('form_validation');        
	    $this->load->library('datatables');
	    $this->load->library('uuid');
    }

    public function index()
    {
        // $this->load->model('Freezer_out_model');
        $data['person'] = $this->Freezer_out_model->getLabtech();
        $data['type'] = $this->Freezer_out_model->getSampleType();
        $data['vessel'] = $this->Freezer_out_model->getVessel();
        $data['destination'] = $this->Freezer_out_model->getDestination();
        $data['shipping'] = $this->Freezer_out_model->getShipping();
        $this->template->load('template','Freezer_out/index', $data);
    } 
    
    public function json() {
        header('Content-Type: application/json');
        echo $this->Freezer_out_model->json();
    }

    public function save() 
    {
        $mode = $this->input->post('mode',TRUE);
        $id = $this->input->post('id_freez',TRUE);
        $dt = new DateTime();

        if ($mode=="insert"){
            $data = array(
            'date_out' => $this->input->post('date_out',TRUE),
            'id_person' => $this->input->post('id_person',TRUE),
            'id_sample' => $this->input->post('id_sample',TRUE),
            'id_vessel' => $this->input->post('id_vessel',TRUE),
            'barcode_sample' => strtoupper($this->input->post('barcode_sample',TRUE)),
            'id_destination' => $this->input->post('id_destination',TRUE),
            'id_shipping' => $this->input->post('id_shipping',TRUE),
            'tracking_number' => $this->input->post('tracking_number',TRUE),
            'comments' => trim($this->input->post('comments',TRUE)),
            'uuid' => $this->uuid->v4(),
            'lab' => $this->session->userdata('lab'),
            'user_created' => $this->session->userdata('id_users'),
            'date_created' => $dt->format('Y-m-d H:i:s'),
            );
 
            $this->Freezer_out_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');    
        }
        else if ($mode=="edit"){
            $data = array(
            'date_out' => $this->input->post('date_out',TRUE),
            'id_person' => $this->input->post('id_person',TRUE),
            'id_sample' => $this->input->post('id_sample',TRUE),
            'id_vessel' => $this->input->post('id_vessel',TRUE),
            'barcode_sample' => strtoupper($this->input->post('barcode_sample',TRUE)),
            'id_destination' => $this->input->post('id_destination',TRUE),
            'id_shipping' => $this->input->post('id_shipping',TRUE),
            'tracking_number' => $this->input->post('tracking_number',TRUE),
            'comments' => trim($this->input->post('comments',TRUE)),
            // 'uuid' => $this->uuid->v4(),
            'lab' => $this->session->userdata('lab'),
            'user_updated' => $this->session->userdata('id_users'),
            'date_updated' => $dt->format('Y-m-d H:i:s'),
            );

            $this->Freezer_out_model->update($id, $data);
            $this->session->set_flashdata('message', 'Create Record Success');    
        }

        redirect(site_url("Freezer_out"));
    }

    public function delete($id) 
    {
        $row = $this->Freezer_out_model->get_by_id($id);
        // $id_user = $this->input->get('id', TRUE);
        // $lab = $this->input->post('id_lab');
        $data = array(
            'flag' => 1,
            );

        if ($row) {
            // $this->Freezer_out_model->delete($id);
            $this->Freezer_out_model->update($id, $data);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('Freezer_out'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Freezer_out'));
        }
    }

    public function get_dna_type() 
    {
        $id = $this->input->get('id1');
        // echo $id;
        $data = $this->Freezer_out_model->getDNAType($id);

        header('Content-Type: application/json');
        echo json_encode($data);
        // return $this->response->setJSON($data);
        // $data['location'] = $this->O3_filter_paper_model->find_loc($id);
    }

    public function valid_bs() 
    {
        $id = $this->input->get('id1');
        // echo $id;
        $data = $this->Freezer_out_model->validate1($id);

        header('Content-Type: application/json');
        echo json_encode($data);
        // return $this->response->setJSON($data);
        // $data['location'] = $this->O3_filter_paper_model->find_loc($id);
    }


    // public function _rules() 
    // {
	// $this->form_validation->set_rules('delivery_number', 'delivery number', 'trim|required');
	// $this->form_validation->set_rules('date_delivery', 'date delivery', 'trim|required');
	// $this->form_validation->set_rules('id_customer', 'id customer', 'trim|required');
	// $this->form_validation->set_rules('expedisi', 'expedisi', 'trim');
	// $this->form_validation->set_rules('receipt', 'receipt', 'trim');
	// // $this->form_validation->set_rules('sj', 'sj', 'trim|required');
	// $this->form_validation->set_rules('notes', 'notes', 'trim');

	// $this->form_validation->set_rules('id_delivery', 'id_delivery', 'trim');
	// $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    // }


    public function excel()
    {
        // $date1=$this->input->get('date1');
        // $date2=$this->input->get('date2');

        $spreadsheet = new Spreadsheet();    
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', "ID"); 
        $sheet->setCellValue('B1', "Date_out"); 
        $sheet->setCellValue('C1', "Lab_tech");
        $sheet->setCellValue('D1', "Sample_type");
        $sheet->setCellValue('E1', "Vessel_type");
        $sheet->setCellValue('F1', "Barcode_vessel");
        $sheet->setCellValue('G1', "Destination");
        $sheet->setCellValue('H1', "Shipping_method");
        $sheet->setCellValue('I1', "Tracking_number");
        $sheet->setCellValue('J1', "Comments");
        // $sheet->getStyle('A1:H1')->getFont()->setBold(true); // Set bold kolom A1

        // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
        $rdeliver = $this->Freezer_out_model->get_all();
    
        // $no = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 2; // Set baris pertama untuk isi tabel adalah baris ke 4
        foreach($rdeliver as $data){ // Lakukan looping pada variabel siswa
          $sheet->setCellValue('A'.$numrow, $data->id);
          $sheet->setCellValue('B'.$numrow, $data->date_out);
          $sheet->setCellValue('C'.$numrow, $data->initial);
          $sheet->setCellValue('D'.$numrow, $data->sample);
          $sheet->setCellValue('E'.$numrow, $data->vessel);
          $sheet->setCellValue('F'.$numrow, $data->barcode_sample);
          $sheet->setCellValue('G'.$numrow, $data->destination);
          $sheet->setCellValue('H'.$numrow, $data->shipping_method);
          $sheet->setCellValue('I'.$numrow, $data->tracking_number);
          $sheet->setCellValue('J'.$numrow, trim($data->comments));
        //   $no++; // Tambah 1 setiap kali looping
          $numrow++; // Tambah 1 setiap kali looping
        }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
    $datenow=date("Ymd");
    $fileName = 'Freezer_Sample_OUT_'.$datenow.'.csv';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=$fileName"); // Set nama file excel nya
    header('Cache-Control: max-age=0');

    // $this->output->set_header('Content-Type: application/vnd.ms-excel');
    // $this->output->set_header("Content-type: application/csv");
    // $this->output->set_header('Cache-Control: max-age=0');
    $writer->save('php://output');
    //     $writer->save($fileName); 
    // //redirect(HTTP_UPLOAD_PATH.$fileName); 
    // $filepath = file_get_contents($fileName);
    // force_download($fileName, $filepath);

        // // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        // $sheet->getDefaultRowDimension()->setRowHeight(-1);
    
        // // Set orientasi kertas jadi LANDSCAPE
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    
        // // Set judul file excel nya
        // $sheet->setTitle("Delivery Reports");
    
        // // Proses file excel
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment; filename="Delivery_Reports.xlsx"'); // Set nama file excel nya
        // header('Cache-Control: max-age=0');
    
        // // $writer = new Xlsx($spreadsheet);
        // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        // // $fileName = $fileName.'.csv';
        // $writer->save('php://output');
           
    }
}

/* End of file Freezer_out.php */
/* Location: ./application/controllers/Freezer_out.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2022-12-14 03:38:42 */
/* http://harviacode.com */