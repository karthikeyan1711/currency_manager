@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Recent Currency List <a href="javascript:void(0);" data-toggle="modal" data-target="#modal-default" class="btn btn-success float-right">Add New Currency</a></div>

                <div class="card-body">
                    <table class="table table-bordered" id="main_category_table">
                       <thead>
                            <tr>
                                <th>ID</th>
                                <th>Currency Name</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add New Currency</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="{{ @route('create-currency') }}" method="post" id="add_currency" role="form">
                    @csrf
                  <div class="row">
                   <div class="col-md-8">
                          <input type="text" name="currency_name[]" minlength="2" maxlength="5"  required value="" class="form-control work_emp_name" placeholder="Currency Name">
                      </div>
                      <div class="col-md-4">
                          <a  href="javascript:void(0);" class="btn btn-primary" onclick="addmorecurrency();">+ Add More</a>
                      </div>
                  </div>
                  <div id="AddMoreSection"></div>
                  <div class="text-center" style="margin-top: 10px;">
                          <input type="submit" name="submit" value="Save Currency" class="btn btn-primary">
                  </div>
                </form>
            </div>
            <div class="modal-footer float-right">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>


      <div class="modal fade" id="edit-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Update Currency</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="{{ @route('update-currency') }}" method="post" id="update_currency" role="form">
                    @csrf
                  <div class="row">
                   <div class="col-md-12">
                          <input type="text" name="currency_name" required value="" minlength="2" maxlength="5" class="form-control" placeholder="Currency Name" id="CurrencyValue">
                      </div>
                  </div>
                  <input type="hidden" name="id" id="CurrencyId">
                  <div class="text-center" style="margin-top: 10px;">
                          <input type="submit" name="submit" value="Update Currency" class="btn btn-primary">
                  </div>
                </form>
            </div>
            <div class="modal-footer float-right">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            
            $('#main_category_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                            "url": "{{ @route('currency-tableAjax') }}",
                            "dataType": "json",
                            "type": "POST",
                            "data":{ _token: "{{csrf_token()}}"}
                        },
                "columns": [
                    { "data": "id" },
                    { "data": "currency_name",},
                    { "data": "options",},
                ]    

            });
        });
    </script>
    <script type="text/javascript">
        function addmorecurrency()
        {
            var html = '<div class="currency_append"><div class="row"><div class="col-md-8"><input type="text" name="currency_name[]" value=""  required minlength="2" maxlength="5" class="form-control work_emp_name" placeholder="Currency Name"> </div><div class="col-md-4"><a  href="javascript:void(0);" class="btn btn-danger remove">Remove</a></div></div></div>';
            $('#AddMoreSection').append(html);
        }

        $("body").on("click",".remove",function(){ 
            $(this).parents(".currency_append").remove();
        });
    </script>
    <script type="text/javascript">
        
        $(document).ready(function(){
            $('#add_currency').on('submit', function(){
                var lngtxt=($(this).find('input[name="currency_name[]"]').val()).length;
                console.log(lngtxt);
                if (lngtxt==0){
                    alert('please enter value');
                    return false;
                }
            });
        });
    </script>
    <script type="text/javascript">
        function Geteditform(CurrencyId,CurrencyValue)
        {
            $('#CurrencyId').val(CurrencyId);
            $('#CurrencyValue').val(CurrencyValue);
            //console.log(CurrencyId+'-'+CurrencyValue);
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

