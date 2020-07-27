<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CurrencyList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function store(Request $request)
    {
        if(!empty($_POST['currency_name']))
        {
            $aleredy_exists = array();

            foreach($request->input('currency_name') as $currency_name)
            {
                $where['currency_name'] = $currency_name;
                $select_currency = CurrencyList::where($where)->count();
                
                $url  = 'https://api.exchangeratesapi.io/latest?symbols='.$currency_name;
                try 
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $result = json_decode(curl_exec($ch),true);
                    curl_close($ch);   
                }
                catch (Exception $e) 
                {
                  $result['error'] = curl_error($ch);
                  curl_close($ch);  
                }

                if(!isset($result['error']) && empty($result['error']))
                {   
                    if($select_currency == 0)
                    {
                        $Currency = new CurrencyList;
                        $Currency->currency_name = $currency_name;
                        $SaveCurrency = $Currency->save();
                        $created_currncys[] = $currency_name;
                    }
                    else
                    {
                        $aleredy_exists[] = $currency_name;
                    }  
                }
                else
                {
                    $invalid_currencys[] = $currency_name;
                }               
                
            }

            if( isset($aleredy_exists) && !empty($aleredy_exists))
            {
                return back()->with('error', implode(',',$aleredy_exists).' Is Aleredy Exists');
            }

            if( isset($invalid_currencys) && !empty($invalid_currencys))
            {
               return back()->with('error', implode(',',$invalid_currencys).' Invalid Currency Code');
            }

            if(isset($SaveCurrency) && $SaveCurrency == true)
            {
                return back()->with('success', implode(',', $created_currncys).' Created Successfully');
            }
        } 
    }

    public function update(Request $request)
    {
        $whereData = [['currency_name','=',$request->currency_name],['id','!=',$request->id]];
        $select_currency = CurrencyList::where($whereData)->count();

        $url  = 'https://api.exchangeratesapi.io/latest?symbols='.$request->currency_name;
        try 
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = json_decode(curl_exec($ch),true);
            curl_close($ch);   
        }
        catch (Exception $e) 
        {
          $result['error'] = curl_error($ch);
          curl_close($ch);  
        }
        
        if(!isset($result['error']) && empty($result['error']))
        {   
            if($select_currency == 0)
            {
                $Currency = CurrencyList::find($request->id);
                $Currency->currency_name = $request->currency_name;
                $SaveCurrency = $Currency->save();
            }
            else
            {
                return back()->with('error', 'Currency Aleredy Exists');   
            }
        }
        else
        {
            return back()->with('error', 'Invalid Currency');   
        }

        if($SaveCurrency)
        {
            return back()->with('success', 'Currency updated successfully.');
        }
    }

    function delete(Request $request)
    {
        $Id = $request->segment(2);
        $CurrencyList =  CurrencyList::find($Id);
        $CurrencyList->status = 'deleted';
        $CurrencyList->save();
        return back()->with('success', 'Currency deleted successfully.');
    }

    public function compare_currency(Request $request)
    {
        $whereData = [['status', '!=', 'deleted']];
        $currencylist = CurrencyList::where($whereData)->get()->toArray();

        if(isset($currencylist) && !empty($currencylist))
        {
            $data['currencylist'] = $currencylist;
        }
        $data['title'] = 'Currency Compare';
        return view('compare_currency',$data);
    }

    public function compare_with_base_currency(Request $request)
    {
        $whereData = [['id', '=', $request->segment(2)]];
        $currencylist = CurrencyList::where($whereData)->get()->toArray();
        $data['currencylist'] = $currencylist;

        $whereDatashow = [['id', '!=', $request->segment(2)],['status', '!=', 'deleted']];
        $currencylisttoshow = CurrencyList::where($whereDatashow)->get()->toArray();
        $data['currencylisttoshow'] = $currencylisttoshow;

        $data['title'] = 'Currency Compare';
        return view('compare_with_base_currency',$data);
    }

    public function append_more(Request $request)
    {
        $whereDatashow = [['id', '!=', $request->input('base')],['status', '!=', 'deleted']];
        $currencylisttoshow = CurrencyList::where($whereDatashow)->get()->toArray();
        $next_inc =  $request->input('next_inc');
        ?>
        <div class="currency_append">
            <div class="row">
                <div class="col-md-8">
                    <select class="form-control currency_to_select" name="currency_name[]" id="currency_to_select-<?Php echo $next_inc; ?>">
                        <option value="">Select Currency</option>
                            <?php
                              if(isset($currencylisttoshow) && !empty($currencylisttoshow))
                              {
                                foreach($currencylisttoshow as $toshow)
                                {
                             ?>
                                  <option value="<?php echo $toshow['id']; ?>"><?php echo $toshow['currency_name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <a  href="javascript:void(0);" class="btn btn-danger remove">Remove</a>
                </div>
            </div>
        </div>
        <?php
    }

    public function ajax_comparison(Request $request)
    {
        $whereDatashow = [['status', '!=', 'deleted']];
        $currencylisttoshow = CurrencyList::where($whereDatashow)->get()->toArray();

        if(isset($currencylisttoshow) && !empty($currencylisttoshow))
        {
            foreach($currencylisttoshow as $toshow)
            {
                $currecy_list[$toshow['id']] = $toshow['currency_name'];
            }
        }

        $basecurrency = $currecy_list[$request->input('base')];
        
        foreach($request->input('currency_name') as $currency_name)
        {
            $currecy_symbols[] = $currecy_list[$currency_name];
        }

        $symbols = implode(',',$currecy_symbols);   

        $url  = 'https://api.exchangeratesapi.io/latest?base?'.$basecurrency.'&symbols='.$symbols;
        try 
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = json_decode(curl_exec($ch),true);
            curl_close($ch);   
        }
        catch (Exception $e) 
        {
          $result['error'] = curl_error($ch);
          curl_close($ch);  
        }

        if(isset($result['rates']) && !empty($result['rates']))
        {
            ?>
            <h2 style="margin-top: 40px;text-align: center;">Compared With Base Currency</h2>
            <div class="row justify-content-center" style="margin-top: 10px">
                <div  class="col-md-8">
                <table class="table table-borderd">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                <?php
                foreach($result['rates'] as $currency => $rates)
                {
                   ?>
                   <tr>
                        <td>
                            <label><?Php echo $currency; ?></label>
                        </td>
                         <td>
                            <label><?Php echo $rates; ?></label>
                        </td>
                    </tr>
                   <?php
                }
                ?>
            </table>
            </div>
        </div>
             <?php
        }
        else
        {
            ?>
            <div class="row">
                <h2 class="text-center">No Result Found</h2>
            </div>
            <?Php
        }
    }

    public function currencytableAjax(Request $request)
    {

        $columns = array( 
            0 => 'id', 
            1 => 'currency_name',
        );

        $totalData = CurrencyList::count();     
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        if(isset($columns[$request->input('order.0.column')]))
        {
            $order = $columns[$request->input('order.0.column')];
        }
        else
        {
            $order = 'id';
        }

        $dir = $request->input('order.0.dir');
        $whereData = [['status', '!=', 'deleted']];
        
        if(empty($request->input('search.value')))
        {            
            $currency_lists = CurrencyList::offset($start)
                    ->where($whereData)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 
            $currency_lists =  CurrencyList::where('id','LIKE',"%{$search}%")
                        ->where($whereData)
                        ->orWhere('currency_name', 'LIKE',"%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
            $totalFiltered = CurrencyList::where('id','LIKE',"%{$search}%")
                        ->where($whereData)
                        ->orWhere('currency_name', 'LIKE',"%{$search}%")
                        ->count();
        }

        $data = array();
        
        if(!empty($currency_lists))
        {
            foreach ($currency_lists as $currency_list)
            {
                $Onclick =  'Geteditform("'.$currency_list->id.'","'.$currency_list->currency_name.'")';
                $Delete =  @route('delete-currency',['id'=>$currency_list->id]);

                $Edit =  'javascript:void(0);';
                $ReturnConfirm = 'return confirm("Are you sure you want to delete this item");';
                
                $nestedData['id'] = $currency_list->id;
                $nestedData['currency_name'] = $currency_list->currency_name;
                $nestedData['options'] = "<a href='$Edit' data-toggle='modal' data-target='#edit-default' title='Edit' onclick='$Onclick' class='btn btn-info'>Edit</a> <a href='$Delete' title='Trash' onclick='$ReturnConfirm' class='btn btn-danger'>Delete</a>";
                $data[] = $nestedData;

            }
        }

        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        );

        echo json_encode($json_data); 
    }
}
