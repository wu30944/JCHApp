<?php 
    namespace App\Repositories;

    use Illuminate\Http\Request;
    use Models\sc_function;
    use Models\codtbld;
    use Validator;
    use Response;
    use DB;


    class SCFunctionRepository
    {
	  	private $dtSCFunction;

        public function __construct(sc_function $data)
        {
            $this->dtSCFunction=$data;
        }

        public function getAll()
        {
        	return $this->dtSCFunction->all();
        }

        /*
         * 取得功能樹的樹根 */
        public function getFunctionRoot()
        {
            return $this->dtSCFunction::where('function_id','=','parent_function')->get();
        }

        /*
         * 取得功能樹中的功能 */
        public function getFunctionSon()
        {
            return $this->dtSCFunction::where('function_id','<>','parent_function')->get();
        }

        public function save_d(Request $request)
        {
            // \Debugbar::info($request->staff_id);
            $data = $this->dtSCFunction->find($request->staffd_id);

            $data->content = $request->editor1;
            $data->save ();

            return  collect(['ServerNo'=>'200','Result' =>'儲存成功！']);
        }

        public function create_staff_d($staff_id,$staff_name,$cod_id)
        {   

            $data = new staffs_d();
            $data->staff_id=$staff_id;
            $data->name=$staff_name;
            $data->cod_id=$cod_id;
            $data->content="";
            $data->create_date=date("Y-m-d");;
            $data->save ();

        }



        public function save(Request $request)
        {

            $rules = array (
                'name'=> 'required',
                'duty'=>'not_in:choice',
                'sdate'=>'required',
                'edate'=>'required'
            );
            $messages = ['name.required' => '姓名欄位不能空白'
                         ,'duty.not_in'=>'請選擇職務'];
            // \Debugbar::info($request->name);
            // \Debugbar::info($request->duty);
            // \Debugbar::info($request->sdate);
            // \Debugbar::info($request->edate);
            $validator = Validator::make ( $request->all(), $rules,$messages );
            \Debugbar::info($request->duty);
            if ($validator->fails ()){       
                 // return Response::json ( 
                 //    array ('errors' => $validator->messages()->all() ));
                  return  collect(['ServerNo'=>'404','Result' =>  $validator->messages()->all()]);
                  // return response()->json(['0' => '404','Result' =>  $validator->messages()->all()]);
            }
            else {
                      // \Debugbar::info('2');          
                if($request->id==NULL)
                {
                    $data = new staff();

                    $data->name = $request->name;
                    $data->cod_id = $request->duty;
                    $data->sdate = $request->sdate;
                    $data->edate = $request->edate;
                    $data->save ();
                    $this->create_staff_d($data->id,$request->name,$request->duty);

                }else{
                    $data = $this->dtStaff->find($request->id);

                    $data->name = $request->name;
                    $data->cod_id = $request->duty;
                    $data->sdate = $request->sdate;
                    $data->edate = $request->edate;
                    $data->save ();

                    /*
                        20170831. 為了要同步staff與 staffs_d兩個table的人員資料
                        所以在這裡也必須要儲存staffs_d中的cod_id欄位資料，不然當修改
                        職務時，就會發生錯誤
                    */
                    $staffd_id = $this->dtSCFunction->where('staff_id','=',$request->id)->pluck('id');
                    // \Debugbar::info(count($staffd_id));
                    if(count($staffd_id)>0)
                    {   
                        $data_d=$this->dtSCFunction->find($staffd_id);
                        $data_d->cod_id = $request->duty;
                         $data_d->save ();
                    }
                    

                }

                // Session::flash('message', 'Successfully updated nerd!');
                // return response ()->json ( $data );
                return  collect(['ServerNo'=>'200','Result' =>'儲存成功！','id'=>$data->id]);
                // return response ()->json ( ['0'=>'200','Result'=>'儲存成功！' ]);
            }
        }

        public function delete($id)
        {
            // \Debugbar::info($id);
            if( $this->dtStaff->find($id)->delete() 
                && $this->dtSCFunction->where('staff_id','=',$id)->delete())
            {
                 return  collect(['ServerNo'=>'success','Result' =>'刪除成功！']);
            }else{
                 return  collect(['ServerNo'=>'fails','Result' =>'刪除失敗！']);
            }

        }

        /*
            2017/08/21. 取出牧師相關資訊
        */
        public function getPastor()
        {
            $strType='duty';
            $strStaff='1';
            return  $dtNews=DB::select('select a.id,a.name,a.image_path,a.sdate
                                        ,a.edate,b.cod_val
                                         from staffs a
                                         join codtbld b
                                         on b.cod_type= ?
                                         and b.cod_id=a.cod_id 
                                         where a.cod_id= ?', [$strType,$strStaff]);
        }  


        /*
            2017/08/29. 取出職務明細
        */
        public function getPastorD($staff_id,$staff_name)
        {
            return $this->dtSCFunction::select('id','content')->where('staff_id','=',$staff_id)->where('name','=',$staff_name)->get();
            // return          DB::select('select a.content
            //                              from staffs_d a
            //                              where a.staff_id= ?
            //                              and a.name=?', [$staff_id,$staff_name]);
        }  

        /*
            2017/08/29. 取出所有的牧師明細
        */
        public function getStaffD($duty_id)
        {
            return $this->dtSCFunction::select('name','content')->where('cod_id','=',$duty_id)->get();
            // return          DB::select('select a.content
            //                              from staffs_d a
            //                              where a.staff_id= ?
            //                              and a.name=?', [$staff_id,$staff_name]);
        }  
        /*
            撈出長老
        */
        public function getElder()
        {
            $strType='duty';
            $strStaff='2';
            return  $dtNews=DB::select('select a.id,a.name,a.image_path,a.sdate
                                ,a.edate,b.cod_val
                                 from staffs a
                                 join codtbld b
                                 on b.cod_type= ?
                                 and b.cod_id=a.cod_id 
                                 where a.cod_id= ?', [$strType,$strStaff]);
        }


        /*
            撈出執事
        */
        public function getDeacon()
        {
            $strType='duty';
            $strStaff='3';
            return  $dtNews=DB::select('select a.id,a.name,a.image_path,a.sdate
                                ,a.edate,b.cod_val
                                 from staffs a
                                 join codtbld b
                                 on b.cod_type= ?
                                 and b.cod_id=a.cod_id 
                                 where a.cod_id= ?', [$strType,$strStaff]);
        }

        /*
        上傳圖片的function
        2017/05/13 
        */
        public function PhotoUpload(Request $request,$id)
        {   
            $file = $request->file('image');
             // $file = $request->file('image');

            $catalog = '/staff';

            //必須是image的驗證
            $input = array('image' => $file);
            $rules = array(
                'image' => 'image'
            );
            
            $validator = \Validator::make($input, $rules);
            if ( $validator->fails() ) {
                // return \Response::json([
                //     'success' => false,
                //     'errors' => $validator->getMessageBag()->toArray()
                // ]);
                return collect(['ServerNo'=>'404','Result' =>$validator->getMessageBag()->toArray()]);
            }else{       
                
                $destinationPath = public_path().env('PHOTO_PATH').$catalog;


                if (!is_dir($destinationPath))
                {
                    mkdir($destinationPath);
                }
                
                //都將檔案存成jpg檔案 命名方式依照團契的ＩＤ命名,這樣就不會有重複的問題
                $filename = $id.'.jpg';//$file->getClientOriginalName();

                // $dataID=$this->dtStaff->where('id','=',$id)->where('cod_id','=',$request->duty)->pluck('id');
                $data = staff::find($id);
                \Debugbar::info($filename);
                $data->image_path = env('PHOTO_PATH').$catalog.'/'.$filename;
                $data->save();


                //  移動檔案
                if(!$file->move($destinationPath,$filename)){
                    // return response()->json(['ServerNo' => '404','Result' => '圖片儲存失敗！']);
                    return collect(['ServerNo'=>'404','Result' => '圖片儲存失敗！']);
                }

                 return collect(['ServerNo'=>'200','Result'=>'照片上傳成功！']);
                // return response()->json(['ServerNo' => '200','Result' => '照片上傳成功！']);
            }
            // return \Response::json([
            //     'success' => true,
            //     'name' => $filename,
            // ]);
            
        }

        public function getOrderByPageing($num)
        {
             return $this->dtStaff->orderBy('cod_id','desc')->paginate($num);
        }
    }