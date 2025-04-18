<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends CI_Controller
{
    public function getHistory($page=1,$rowsPerPage=10)
    {
        $offset = (($page-1)*$rowsPerPage);
        $fields = ['details','date'];
        if($_SESSION['user_level'] == 'Root') $fields[] = 'author';
        $totalRows = $this->db->count_all("commits");
        $result = $this->db->select($fields)->from("commits")->order_by("date","desc")->limit($rowsPerPage,$offset)->get()->result();
        echo json_encode(array(
            "result"=>true,
            "commits"=>$result,
            "pagination"=>$this->pagination("ajax/misc/getHistory",$totalRows,$rowsPerPage,4)
        ));
        exit;
    }

    private function pagination($url,$total_rows,$per_page=10,$uri_segment=4)
    {
        $config['base_url'] = base_url($url);
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $uri_segment;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 10;
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['next_link'] = false;
        $config['prev_link'] = false;
        $config['num_tag_open'] = "<li>";
        $config['num_tag_close'] = "</li>";
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['reuse_query_string'] = TRUE;
        $config['cur_tag_close'] = "</a></li>";
        $CI = & get_instance();
        $CI->load->library('pagination');
        $CI->pagination->initialize($config);
        $pagination = $CI->pagination->create_links();
        return $pagination;
    }

    public function reorder()
    {
        $table = $this->input->post('table_name');
        $pk = $this->input->post('primary_key');
        $display_order_field = $this->input->post('display_order_field');
        $json = $this->input->post('data');
        $data = json_decode($json);
        $vars = [];
        foreach($data as $item){
            $vars[] = array(
                $display_order_field    =>  $item->{$display_order_field},
                $pk                     =>  $item->id
            );
        }
        $this->db->update_batch($table,$vars,$pk);
        
    }

    public function delete()
    {
        $table = $this->input->post("table");
        $id = $this->input->post("id");
        $uuid = $this->input->post("uuid");
        $pk = $this->input->post("pk");
        $this->db->set("status","0")
        ->where($pk,($pk=='id')?$id:$uuid)
        ->update($table);
        
        echo json_encode(array("result"=>true));
    }

    public function getColors()
    {
        $data = $this->db->select("id,uuid,label AS color, color_swatch")
        ->from("vehicle_colors")
        ->where("status","1")
        ->order_by("label")
        ->get()
        ->result();

        if(!empty($data)){
            echo json_encode(array("result"=>true,"data"=>$data,"rows"=>count($data)));
        }else{
            echo json_encode(array("result"=>false));
        }
    }

    public function dashboard_update()
    {

        $this->load->model("users_model");
        $this->load->model('expenses_model');
        $this->load->model("delivery_model");

        $totals = $this->delivery_model->getTotals();
        $deliveryBoys = $this->delivery_model->getTotalsPerDeliveryBoy();

        echo json_encode(array(
            'result'        =>  true,
            'totals'        =>  $totals,
            'deliveryBoys'  =>  $deliveryBoys
        ));

    }

    public function monitor()
    {
        $requests = $this->db->select("dr.*,u.name AS delivery_boy,s.document_number,s.customer_details,p.product_name")
                        ->from("discount_request dr")
                        ->join("users u","u.id=dr.created_by","left")
                        ->join("sales_details sd","sd.id=dr.sales_details_id")
                        ->join("products p","p.product_id=sd.product_id")
                        ->join("sales s","s.id=sd.sale_id")
                        ->where("dr.status","draft")
                        ->get()->result();

        echo json_encode(array("result"=>true,"requests"=>$requests));
    }

    public function search_items()
    {
        $term = $this->input->post('data')['term'];
        $search_in = $this->input->post('search_in');

        $this->db->select("p.*")
                ->from("products p")
                ->join("product_categories pc","pc.id=p.category_id","left");
        if(!empty($search_in)) {
            $this->db->group_start();
            foreach($search_in as $i => $column){
                if($i==0){
                    $this->db->like($column, $term);
                }else{
                    $this->db->or_like($column, $term);
                }

            }
            $this->db->group_end();
        }                
        $result = $this->db->where("p.status","1")
                        ->get()
                        ->result();

        echo json_encode(array("result" => true, "products" => $result));
    }

    public function getCustomerDetails()
    {
        $this->load->model("customers_model");
        $customer = $this->customers_model->get($this->input->post("uuid"));
        echo json_encode(array("result"=>true,"customer"=>$customer));
        exit;
    }

    public function getProducts()
    {
        $this->load->model("products_model");
        $products = $this->products_model->get($this->input->get());
        echo json_encode(array("result"=>true,"products"=>$products));
        exit;
    }

    public function getProduct()
    {
        $this->load->model("products_model");
        $product = $this->products_model->get($this->input->post('uuid'));
        echo json_encode(array("result"=>true,"product"=>$product));
        exit;
    }

    public function lookup()
    {
        $options = $this->input->post("options");
        $result = $this->db->select("*")->from($options['table'])->where("status",$options['status'])->get()->result();
        echo json_encode(array("result"=>true,"data"=>$result));
        exit;
    }

    public function getParam()
    {
        $this->load->model("system_model");
        $result = $this->system_model->getParam($this->input->get('param'));
        echo json_encode(array("result"=>true,"param"=>$result));
        exit;
    }

    public function getImages()
    {
        $ids = $this->input->post("images");
        $images = $this->db->select("id,file_name")->from("orders_images")->where_in("id",$ids)->get()->result();
        echo json_encode(array("result"=>true,"images"=>$images));
        exit;
    }

    public function searchSerialNumber()
    {
        $text = $this->input->post("text");
        $query = "SELECT 'DN' as type, `p`.`stockref`, `sn`.`serial_number`, `d`.`uuid`, `d`.`document_number`, DATE(`d`.`deliverynote_date`) AS 'date', `c`.`company_name`
                    FROM `product_serialnumbers` `sn`
                    JOIN `deliverynote_details` `dd` ON `dd`.`id`=`sn`.`deliverynote_details_id`
                    JOIN `products` `p` ON `p`.`id`=`dd`.`product_id`
                    JOIN `deliverynotes` `d` ON `d`.`id`=`dd`.`deliverynote_id`
                    JOIN `customers` `c` ON `c`.`customer_id` = `d`.`customer_id`
                    WHERE `serial_number` LIKE '%$text%' ";
        $query .= "UNION ";
        $query .= "SELECT 'GR' as type, `p`.`stockref`, `sn`.`serial_number`, `g`.`uuid`, `g`.`document_number`, DATE(`g`.`goodsreceive_date`) AS 'date', `s`.`company_name`
                    FROM `product_serialnumbers` `sn`
                    JOIN `goodsreceive_details` `gd` ON `gd`.`id`=`sn`.`goodsreceive_details_id`
                    JOIN `products` `p` ON `p`.`id`=`gd`.`product_id`
                    JOIN `goodsreceive` `g` ON `g`.`id`=`gd`.`goodsreceive_id`
                    JOIN `suppliers` `s` ON `s`.`id` = `g`.`supplier_id`
                    WHERE `serial_number` LIKE '%$text%' ";
        $query .= "ORDER BY date ASC";

        $result = $this->db->query($query)->result();

        // $result =  $this->db->select("p.stockref,sn.serial_number,d.document_number")
        //                 ->from("product_serialnumbers sn")
        //                 ->join("deliverynote_details dd","dd.id=sn.deliverynote_details_id")
        //                 ->join("products p","p.id=dd.product_id")
        //                 ->join("deliverynotes d","d.id=dd.deliverynote_id")
        //                 ->where("serial_number","$text")
        //                 ->get()
        //                 ->result();
        echo json_encode(array("result"=>(count($result)>0)?true:false,"rows"=>$result,"query"=>$this->db->last_query()));
        exit;
    }

    public function getRate()
    {
        $currency_id = $this->input->post("currency_id");
        $result = $this->db->select("*")->from("currencies")->where(array("id"=>$currency_id,"status"=>"1"))->get()->row();
        echo json_encode(array("result"=>true,"rate"=>$result->rate));
        exit;
    }
}