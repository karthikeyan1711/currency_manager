@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Compare Currency</a></div>
                <div class="card-body">
                   <form action="javascript:void(0);" method="get">
                      <div class="row justify-content-center">
                        <div class="col-md-6">
                          <label>Select Base Currency</label>
                          <select class="form-control" name="currency" id="currency">
                              <option value="">Select Currency</option>
                              @if(isset($currencylist))
                                @foreach($currencylist as $list)
                                  <option  value="{{ $list['id'] }}">{{ $list['currency_name'] }}</option>
                                @endforeach
                              @endif
                          </select>
                        </div>
                      </div>
                      <div class="text-center" style="margin-top: 10px;">
                            <input type="button" onclick="checkvalues();" name="compare" value="Next" class="btn btn-primary">
                        </div>
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
            var html = '<div class="currency_append"><div class="row"><div class="col-md-8"><input type="text" name="currency_name[]" value=""  required minlength="2" maxlength="5" class="form-control work_emp_name" placeholder="Currency Name"> </div><div class="col-md-4"><a  href="javascript:void(0);" class="btn btn-danger remove">Remove</a></div></div></div>';
            $('#AddMoreSection').append(html);
        }

        $("body").on("click",".remove",function(){ 
            $(this).parents(".currency_append").remove();
        });

        function checkvalues()
        {
            var Redirecturl = "{{ url('currency-compare') }}";

            var currency = $('#currency').val();

            if(currency == '' || currency == null)
            {
                alert('Please choose the base currency');
                $('#currency').focus();
                return false;
            }
            else
            {
                window.location.href = Redirecturl + '/' + currency;
            }
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

