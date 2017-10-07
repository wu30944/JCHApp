@extends('admin.layouts.base')
@section('title','輪播圖片維護')
@section('pageDesc','DashBoard')
@section('content')

    <section class='container box'>
        <style>
            .table-borderless tbody tr td, .table-borderless tbody tr th,
            .table-borderless thead tr th {
                border: none;
            }
        </style>

        <body>
        <div class="content full ">
            <div class="col-lg-12">
                <h1 class="page-header text-center">@lang('function_title.MACarousel')</h1>
            </div>

            <div class="first">

                @if(Gate::forUser(auth('admin')->user())->check('admin.data.create'))
                    <div class="form-group row add">
                        <br>
                        <div class="col-md-4">
                            <button class="btn btn-primary" type="submit" id="add">
                                <span class="glyphicon glyphicon-plus"></span> @lang('default.add')
                            </button>
                        </div>
                    </div>
                @endif
                <div class="row">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @elseif($message = Session::get('fails'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>

                    @endif
                </div>
                {{ csrf_field() }}
                <div class="table-responsive text-center">
                    {{-- <table class="table table-borderless table-striped" id="gridview"> --}}
                    <table class="table table-borderless table-striped" id="gridview">
                        {{-- <table id="gridview" class="text-center table-striped" cellspacing="0" width="70%"> --}}
                        <thead>
                        <tr>
                            {{-- <th class="text-center">#</th> --}}
                            <th class="hidden"></th>
                            <th class="text-center">是否顯示</th>
                            <th class="text-center">輪播圖片名稱</th>
                            <th class="text-center">顯示日期</th>
                            @if(Gate::forUser(auth('admin')->user())->check('admin.data.edit'))
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                        </thead>
                        @if(isset($dtCarousel) and count($dtCarousel)>0)
                            @foreach($dtCarousel as $item)
                                <tr class="item{{$item->id}}">
                                    {{-- <td>{{$item->id}}</td> --}}
                                    <td class="hidden">{{$item->id}}</td>
                                    <td>{{Form::radio('choice'.$item->id,$item->id,$item->is_show,['id'=>'show_'.$item->id])}}
                                    </td>
                                    <td>{{$item->photo_name}}</td>
                                    <td>{{$item->show_date}}</td>
                                    <td>
                                        @if(Gate::forUser(auth('admin')->user())->check('admin.data.edit'))
                                            <button class="edit-modal btn btn-info"
                                                    data-info="{{$item->id}}">
                                                <span class="glyphicon glyphicon-edit"></span> @lang('default.edit')
                                            </button>
                                        @endif
                                        @if(Gate::forUser(auth('admin')->user())->check('admin.data.destory'))
                                            <button class="delete-modal btn btn-danger"
                                                    data-info="{{$item->id}}">
                                                <span class="glyphicon glyphicon-trash"></span> @lang('default.delete')
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <div>
                                <div class="alert alert-danger alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>目前無任何資料</strong>
                                </div>
                                {{--@lang('message.NoNews')--}}
                            </div>
                        @endif
                        <div>
                            <div class="alert alert-success alert-block hide" id="AlertBox">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong id="AlertMessage"></strong>
                            </div>
                            {{--@lang('message.NoNews')--}}
                        </div>
                    </table>
                </div>
            </div>

            <div class="second hide" id="DivSecond">
                <!-- Intro Content -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="control-label col-md-2 col-form-label" for="photo_name">輪播圖片名稱:</label>
                                <div class="col-md-4">
                                    {!!form::text('photo_name','',['class'=>'form-control','id'=>'photo_name'])!!}
                                </div>
                            </div>
                            <div class="form-group row">
                                    <label class="control-label col-md-2" for="show_date">顯示日期:</label>
                                    <div class="col-md-2">
                                        {!!form::text('show_date','',['class'=>'form-control datepicker','id'=>'show_date'])!!}
                                    </div>
                            </div>
                            {!!form::text('CarouselID','',['class'=>'form-control','id'=>'CarouselID'])!!}
                            <div class="size"></div>
                            <div align="center">
                                <img class="img-responsive preview"  alt="" id="preview" src="http://via.placeholder.com/2156x350">
                            </div>
                            <a href="#" class="btn button-change-profile-picture">

                                <label for="upload-profile-picture">
                                    {{--<input type="file" name="file[]" multiple="multiple" required="required" draggable="true" class="upl"/>--}}
                                    <input name="image" id="image" type="file" class="manual-file-chooser js-manual-file-chooser js-avatar-field upl" >
                                </label>
                            </a>
                            <span id="upload-avatar"></span>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="add_modal-footer">
                    <p class="error text-center alert alert-danger hidden"></p>

                    <button type="button" class="btn actionBtn" data-dismiss="modal" id="addbtn">
                        <span id="update_action_button" class='glyphicon'> </span>
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal" id="ctlCANCEL">
                        <span class='glyphicon glyphicon-remove'></span> 取消
                    </button>
                </div>
            </div>
        </div>

        <div id="DeleteModel" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@lang('default.delete')</h4>
                    </div>
                    {!! Form::open(['route'=>'MADeleteCarousel','id'=>'FormDelete','class'=>'form-horizontal']) !!}

                    <div class="modal-body">
                        <div class="deleteContent" >
                            {!!form::text('DeleteCarouselID','',['class'=>'form-control','id'=>'DeleteCarouselID'])!!}
                            @lang('default.sure_delete') <span class="name"></span> ? <span
                                    class="hidden did"></span>
                        </div>
                        <div class="modal-footer">
                            <p class="error text-center alert alert-danger hidden"></p>

                            <button type="submit" class='btn  btn-danger'>
                                <span class='glyphicon glyphicon-trash'></span>  @lang('default.delete')
                            </button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">
                                <span class='glyphicon glyphicon-remove'></span>  取消
                            </button>
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div id="AddModel" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        {{--<form class="form-horizontal" role="form" action="">--}}
                        {!! Form::open(['route'=>'MACreateCarousel','id'=>'form_add','class'=>'form-horizontal']) !!}

                        <div class="form-group">
                            <label class="control-label col-sm-3" for="photo_name">輪播圖片名稱:</label>
                            <div class="col-sm-9">
                                {!!form::text('photo_name','',['class'=>'form-control','id'=>'photo_name'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="show_date">顯示日期:</label>
                            <div class="col-sm-9">
                                {!!form::text('show_date','',['class'=>'form-control datepicker','id'=>'show_date'])!!}
                            </div>
                        </div>

                        <div class="add_modal-footer" align="right">
                            <p class="error text-center alert alert-danger hidden"></p>
                            <button type="submit" class='btn' id="btnSave">
                                <span class='glyphicon glyphicon-check'></span>  @lang('default.save')
                            </button>

                            <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">
                                <span class='glyphicon glyphicon-remove'></span>  取消
                            </button>
                        </div>
                        {{--</form>--}}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        </body>
    </section>
@stop

@section('js')
    <script src="../js/jquery.validate.js"></script>

    <script src="../ckeditor/ckeditor.js"></script>
    <script src="../js/ckeditor_api.js"></script>
    {{--<script src="js/jquery.shCircleLoader.js" type="text/javascript"></script>--}}
    <script>
        var validator;
        $().ready(function() {

            validator= $("#form_add").validate({
                rules: {
                    fellowship_name:{
                        required: true
                    }
                },
                messages: {
                    fellowship_name: "請輸入團契名稱"
                }
            });
        });

        var objImg;
        var ImgURL;
        /**
         * 格式化
         * @param   num 要轉換的數字
         * @param   pos 指定小數第幾位做四捨五入
         */
        function format_float(num, pos)
        {
            var size = Math.pow(10, pos);
            return Math.round(num * size) / size;
        }
        /**
         * 預覽圖
         * @param   input 輸入 input[type=file] 的 this
         */
        function preview(input) {

//        alert(input.files.length);
            if (!input.files[0].type.match('image.*'))
            {
                alert('您選擇的不是圖片檔案');
                $('#image').attr({value:''});

            }
            else if (input.files && input.files[0] ) {
                var reader = new FileReader();
                objImg=input.files[0];
                reader.onload = function (e) {
                    $('.preview').attr('src', e.target.result);
                    var KB = format_float(e.total / 1024, 2);
                    $('.size').text("檔案大小：" + KB + " KB");
                    ImgURL=e.target.result
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("body").on("change", ".upl", function (){

            preview(this);
        })


        $("#editbtn").on('click', function(){
            if(objImg.type.match('image.*'))
            {
                var formData = new FormData();
                formData.append('image', objImg);
                formData.append('id',$('#CarouselID').val());
                formData.append('photo_name',$('#photo_name').val());
                formData.append('show_date',$('#show_date').val());
                formData.append('_token',$('input[name=_token]').val());
                $.ajax({
                    url: '/admin/MAPhotoUpload',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'post',
                    success: function(data){
                        if(data['ServerNo']=='200'){
                            // 如果成功
                            alert( data['Data'][0].photo_path);
                            $('#ShowImg').attr('src', data['Data'][0].photo_path);

                            // $('input[name=ShowImg]').val(data);
                            /*
                             20170913 註解掉下面這段程式，會有錯誤
                             $(obj).off('change');
                            * */


                        }else{
                            alert('上傳失敗');
                            // 如果失败
                            // alert(data['ResultData']);
                        }
                    }
                });

            }

        });

        $(document).on('click', '.edit-modal', function() {

            var stuff = $(this).data('info');

            $('.first').addClass('hide');
            $('.second').removeClass('hide');
            $('#update_action_button').text(" 更新");
            $('#update_action_button').addClass('glyphicon-check');
            $('#update_action_button').removeClass('glyphicon-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');

            $.ajax({
                type: 'get',
                url: '/admin/MACarouselShow',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id':stuff,
                },
                success: function(data){

                    if(data['ServerNo']=='200')
                    {
                        $('#photo_name').val(data['Data'][0].photo_name);
                        $('#show_date').val(data['Data'][0].show_date);
                        $('#CarouselID').val(data['Data'][0].id);

                        if(data['Data'][0].photo_path=="" ||data['Data'][0].photo_path==undefined){
                            //alert('no photo');
                            $('#preview').attr('src','http://via.placeholder.com/2156x350');
                        }else{
                            $('#preview').attr('src',data['Data'][0].photo_path);


                        }
                    }else{

                    }

                }
            });
        });



        $('.btn-cancel').on('click', function() {

            validator.resetForm();
        });

        $('#ctlCANCEL').on('click', function() {
            $('.second').addClass('hide');
            $('.first').removeClass('hide');
            $("#ShowImg").removeAttr("src");

            $("#DivSecond").find(":text,textarea,input").each(function() {
                $(this).val("");
            });
//            alert($('#photo_name').val());
        });

        $('#add').on('click',function(){

            $('.modal-title').text('新增輪播圖片');
            $('#btnSave').addClass('btn-success');
            $('#form_add').show();
            $('#AddModel').modal('show');

        });


        $(document).on('click', '.delete-modal', function() {

            var stuff = $(this).data('info');
            $('#DeleteCarouselID').val(stuff);
            $('#DeleteModel').modal('show');
        });


        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'post',
                url: '/admin/MADeleteFellowship',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $('#id').val()
                },
                success: function(data) {
                    $('.item' + $('#id').val()).remove();
                }
            });
        });

        $('#addbtn').on('click', function() {
            if(objImg.type.match('image.*'))
            {
                var formData = new FormData();
                formData.append('image', objImg);
                formData.append('id',$('#CarouselID').val());
                formData.append('photo_name',$('#photo_name').val());
                formData.append('show_date',$('#show_date').val());
                formData.append('_token',$('input[name=_token]').val());
                $.ajax({
                    url: '/admin/MAPhotoUpload',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'post',
                    success: function(data){
                        if(data['ServerNo']=='200'){
                            // 如果成功

                            $('#preview').attr('src', data['Data']);

                            $('.second').addClass('hide');
                            $('.first').removeClass('hide');
                            alert(data['Message']);

                        }else{
                            alert(data['ServerNo']);
                            alert('上傳失敗');
                        }
                    },error:function()
                    {
                        alert('拍謝程式有問題，請洽工程師');
                    }
                });

            }else{

            }

        });

        $('.datepicker').datetimepicker({
            yearOffset:0,
            lang:'zh-TW',
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'
        });

        /*
        * 2017/10/09    當使用者點選Radiobox，就使用ajax去儲存使用者選擇的資料
        *               ，此處與Verses.blade.php該之裡面的部分不同，
        *               因為該隻功能是只能夠單選，在此之功能是需要能夠多選
        *               所以兩個部分有所不同
        * */
        $('input[type="radio"]').on('click',function(){
            var is_show = $(this).val();
            var id=$(this).val();

            if(typeof($(this).attr('checked'))!='undefined')
            {
                $(this).attr('checked',false);
                is_show = 0;
            }else {
                $(this).attr('checked',true);
                is_show = 1;
            }


            $.ajax({
                type: 'post',
                url: '/admin/MACarouselIsShow',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'is_show': is_show,
                    'id': id
                },
                success: function(data) {
                    if (data['ServerNo']=='200'){
                        $('#AlertBox').show();
                        $('#AlertMessage').text(data['Message']);
                        $('#AlertBox').removeClass('hide');
                    }
                    else {

                    }
                }
            });
            $(".alert-block").slideToggle(500);
            setTimeout(function () {
                $(".alert-block").hide(200);
            }, 3000);
        });


    </script>
@stop