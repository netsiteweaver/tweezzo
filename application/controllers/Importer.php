<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Importer extends MY_Controller {

    public $data;
    public $handle;
    
    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
    }

    public function index()
    {
        // $this->handle = fopen("./data/report-".date("YmdHis").".txt","a+");
        $filename = getcwd() . "/data/report-".date("YmdHis").".txt";
        $this->handle = fopen($filename,"w");
        if(!$this->handle) {
            echo "Failed opening file";
            return;
        }

        $this->db->truncate("customers");
        $this->write2file("Truncated Customers Master Record");

        $batch1 = $this->customers1("Cust database - edited.csv");
        $this->write2file("Imported 1st Tab in Excel Sheet, with " . $batch1 . " rows");

        $batch2 = $this->customers2("Cust database 2nd part.csv");
        $this->write2file("Imported 2nd Tab in Excel Sheet, with " . $batch2 . " rows");

        $this->write2file("Imported " . ($batch1 + $batch2) . " total rows");

        $this->db->query("UPDATE `customers` SET vat = '' WHERE vat = 'VAT' ");
        $ct = $this->db->affected_rows();
        $this->write2file("Updated " . $ct . " rows : " . "UPDATE `customers` SET vat = '' WHERE vat = 'VAT' ");

        fclose($this->handle);
    }

    private function write2file($text,$verbose = true, $crln=true)
    {
        fwrite($this->handle, date('Y-m-d H:i:s : ') . $text . (($crln)?"\r\n":"") );
        if($verbose) echo $text."<br>";
    }

    public function customers1($filename)
    {
        $customers = "./data/$filename";
        if(!file_exists($customers)) {
            die("File not found");
        }
        $line = 0;
        $clients = [];
        if (($handle = fopen($customers, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                // debug($data,false);
                if($data[0]!='') {
                    switch($data[0]){
                        case 'Client Code' :
                            $clients[$line]['customer_code'] = $data[1];
                            $clients[$line]['fidelity_card'] = $data[3];
                            $clients[$line]['brn']= $data[6];
                            break;

                        case 'Name' :
                            $clients[$line]['last_name'] = $data[1];
                            $myDateTime = DateTime::createFromFormat('d-M-Y', $data[3]);
                            $clients[$line]['dob'] = $myDateTime->format('Y-m-d');
                            $clients[$line]['vat'] = $data[6];
                            break;

                        case 'NIC' :
                            $clients[$line]['nic'] = empty($data[1])?null:$data[1];
                            $clients[$line]['phone_number1'] = $data[3];
                            $clients[$line]['fidelity_card'] = $data[6];
                            break;

                        case 'Address' :
                            $clients[$line]['address'] = $data[1];
                            $clients[$line]['phone_number2'] = $data[3];
                            // $clients[$line]['accum_point'] = $data[6];
                            break;

                        case 'Nationality' :
                            $clients[$line]['nationality'] = empty($data[1])?'Mauritian':$data[1];
                            $clients[$line]['email'] = $data[3];
                            $clients[$line]['uuid'] = gen_uuid();
                            $clients[$line]['marital_status'] = 'single';
                            $clients[$line]['remarks'] = 'Imported 2024-04-24';
                            // $clients[$line]['fid_from'] = $data[6];
                            $line++;
                            break;

                    }
                     
                }
            }

            
        }
        $this->db->insert_batch("customers",$clients);
        // debug($clients,false);
        return count($clients);
    }

    public function customers2($filename)
    {
        $customers = "./data/$filename";
        if(!file_exists($customers)) {
            die("File not found");
        }
        $line = 0;
        $clients = [];
        if (($handle = fopen($customers, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                // debug($data,false);
                if($data[0]!='') {
                    switch($data[0]){
                        case 'Client Code' :
                            $clients[$line]['customer_code'] = $data[1];
                            $clients[$line]['fidelity_card'] = $data[4];
                            $clients[$line]['brn']= $data[8];
                            break;

                        case 'Name' :
                            $clients[$line]['last_name'] = $data[1];
                            $myDateTime = DateTime::createFromFormat('d-M-Y', $data[5]);
                            $clients[$line]['dob'] = $myDateTime->format('Y-m-d');
                            $clients[$line]['vat'] = $data[8];
                            break;

                        case 'NIC' :
                            $clients[$line]['nic'] = empty($data[1])?null:$data[1];
                            $clients[$line]['phone_number1'] = $data[4];
                            $clients[$line]['fidelity_card'] = $data[8];
                            break;

                        case 'Address' :
                            $clients[$line]['address'] = $data[1];
                            $clients[$line]['phone_number2'] = $data[5];
                            // $clients[$line]['accum_point'] = $data[6];
                            break;

                        case 'Nationality' :
                            $clients[$line]['nationality'] = empty($data[1])?'Mauritian':$data[1];
                            $clients[$line]['email'] = $data[4];
                            $clients[$line]['uuid'] = gen_uuid();
                            $clients[$line]['marital_status'] = 'single';
                            $clients[$line]['remarks'] = 'Imported 2024-04-24';
                            // $clients[$line]['fid_from'] = $data[6];
                            $line++;
                            break;

                    }
                     
                }
            }

            
        }
        $this->db->insert_batch("customers",$clients);
        // debug($clients,false);
        return count($clients);
    }

    public function products($filename)
    {
        $rate = 47;
        $currency_id = 1;
        $categories = [];
        $brands = [];

        $customers = "./data/$filename";
        if(!file_exists($customers)) {
            die("File not found");
        }
        $line = 0;
        $clients = [];
        if (($handle = fopen($customers, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                // $this->db->truncate("deliverynote_details");
                // $this->db->truncate("deliverynotes");
                // $this->db->truncate("goodsreceive_details");
                // $this->db->truncate("goodsreceive");
                // $this->db->truncate("products");
                if(is_numeric($data[0])){
                    if(!in_array($data[3],$categories)){
                        $categories[] = $data[3];
                        $this->db->insert("product_categories",array(
                            "uuid"      =>  gen_uuid(),
                            "name"      =>  $data[3],
                            "created_by"    =>  1,
                            "created_date"  =>  date("Y-m-d H:i:s")
                        ));
                        $category_id = $this->db->insert_id();
                    }else{
                        $category_id = $this->db->select("id")->from("product_categories")->where("name",$data[3])->get()->row("id");
                    }
                    $row = array(
                        "uuid"      =>  gen_uuid(),
                        "stockref"      =>  $data[1],
                        "name"          =>  $data[2],
                        "description"   =>  $data[2],
                        "cost_foreign"  =>  floatval(str_replace(",","",$data[4])),
                        "rate"          =>  $rate,
                        "cost_price"    =>  floatval(str_replace(",","",$data[5])),
                        "selling_price" =>  floatval(str_replace(",","",$data[5])),
                        "category_id"   =>  $category_id,
                        "brand_id"      =>  1,
                        "photo"         =>  '',
                        "created_by"    =>  1,
                        "created_date"  =>  date("Y-m-d H:i:s"),
                        "currency_id"   =>  $currency_id
                    );
                    $this->db->insert("products",$row);

                    // create inventory
                    $this->db->insert("inventory",array(
                        "product_id"    =>  $this->db->insert_id(),
                        "department_id" =>  1,
                        "onhand"        =>  0
                    ));
                }
                // debug($data,false);
            }
            
        }
        return count($clients);
    }

}
