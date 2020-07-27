@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Compare Currency <a href="javascript:void(0);" onclick="reloadpage();" class="float-right">Refresh</a></div>
                <div class="card-body">
                   <form action="javascript:void(0);" method="get" id="compare_form">
                      <div class="row justify-content-center">
                        <div class="col-md-3">
                          <label class="font-weight-bold">Base Currency : {{ $currencylist[0]['currency_name'] }}
                        </div>
                          <a href="{{ @route('currency-compare') }}"class="pull-left" >Edit Base Currency</a>
                        
                      </div>

                      <label class="text-center">Select Currency To Compare</label>
                      <div class="row justify-content-center">
                        <div class="col-md-8">
                            
                            <select class="form-control currency_to_select" name="currency_name[]" id="currency_to_select_1">
                              <option value="">Select Currency</option>
                              @if(isset($currencylisttoshow) && !empty($currencylisttoshow))
                                @foreach($currencylisttoshow as $toshow)
                                  <option value="{{ $toshow['id'] }}">{{ $toshow['currency_name'] }}</option>
                                @endforeach
                              @endif
                            </select>
                        </div>
                        <input type="hidden" name="base" value="{!! Request::segment(2) !!}">
                        <div class="col-md-4">
                          <a href="javascript:void(0);" onclick="addmorecurrency();" class="btn btn-info">Add More</a>
                        </div>
                      </div>
                      <div id="AddMoreSection"></div>
                      <div class="text-center" style="margin-top: 10px;">
                            <input type="button" onclick="checkvalues();" name="compare" value="Compare" class="btn btn-primary">
                        </div>
                      <div id="AppendResult"></div>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">
        function addmorecurrency()
        {
          var next_inc = 1;
         if($(".currency_to_select").val() !== '')
         {
               $.ajaxSetup({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                var segment = '{!! Request::segment(2) !!}';

                $.ajax({
                    url : "{{ @route('append_more') }}",
                    type : 'post',
                    data: 'base='+segment+'&next_inc='+next_inc,
                    success:function(result_data)
                    {
                         $('#AddMoreSection').append(result_data);
                    }
                });

                next_inc++;
             }
            else
            {
              alert('please select a currency to append next');
              return false;
            }
        }

        $("body").on("click",".remove",function(){ 
            $(this).parents(".currency_append").remove();
        });

        function checkvalues()
        {
            if($(".currency_to_select").val() !=='')
            {
              var formdata = $('#compare_form').serialize();
              $.ajaxSetup({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                $.ajax({
                    url : "{{ @route('ajax_comparison') }}",
                    type: 'Post',
                    data : formdata,
                    success:function(ResponseData)
                    {
                      $('#AppendResult').html(ResponseData);
                       // console.log(ResponseData);
                    }
                });
            }
        }

        function reloadpage()
        {
          location.reload(true);
        }
       
    </script>
    @if( Session::has( 'success' ))
            <script type="text/javascript">
                toastr.success("{{ Session::get( 'success' ) }}");
            </script>
      @endif
      @if( Session::has( 'error' ))
            <script type="text/javascript">
                toastr.error("{{ Session::get( 'error' ) }}");
            </script>
      @endif
@endsection

