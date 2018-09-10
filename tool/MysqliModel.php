<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21 0021
 * Time: 10:45
 */
namespace peteryang\tool;


class MysqliModel
{


    protected $DbObj=null;


    protected $option=[
        'where'=>'',
        'limit'=>'',
        'table'=>'',
        'order'=>'',
        'group'=>'',
        'fields'=>'',
        'join'=>"",
//        'join_arr'=>[]

    ];

    protected $lastSql="";


    /**
     * MysqliModel constructor.
     * @param $host
     * @param $username
     * @param $password
     * @param $database
     * @param int $port
     */
    function __construct($host,$username,$password,$database,$port=3306)
    {

//        $config=Config::get('database');



        $DbObj=new \mysqli($host,$username,$password,$database,$port);





        if($DbObj->connect_errno){


            throw new \mysqli_sql_exception($DbObj->connect_error);
        }



        $this->DbObj=$DbObj;


    }


    function find(){


        $option=$this->option;

        $this->init_options();


        $where="";

        $fields='';


        if($option['where']) $where="where ".$option['where'];

        if($option['fields']){



            $fields=$option['fields'];



        }else{
            $fields='*';
        }


        $sql="select {$fields} from {$option['table']} {$option['join']} {$where}  limit 1";

        $res=$this->DbObj->query($sql);




        if($this->DbObj->error) throw new \mysqli_sql_exception($this->DbObj->error.",ERROR SQL:".$sql);


        if($res->num_rows==0) return [];

        $row=$res->fetch_assoc();

        $res->close();

        $this->lastSql=$sql;

        return $row;

    }


    function select(){


        $option=$this->option;



        $this->init_options();


        $where="";

        $limit="";

        $order="";

        $group="";

        $fields='';



        if($option['where']) $where="where ".$option['where'];

        if($option['limit']) $limit="limit ".$option['limit'];

        if($option['order']) $order="order by ".$option['order'];

        if($option['group']) $group="group by ".$option['group'];

        if($option['fields']){



            $fields=$option['fields'];



        }else{
            $fields='*';
        }


        $sql="select {$fields} from {$option['table']} {$option['join']}  {$where} {$group}  {$order}  {$limit}";


        $res=$this->DbObj->query($sql);

        if($this->DbObj->error) throw new \mysqli_sql_exception($this->DbObj->error.",ERROR SQL:".$sql);


        $arr=$res->fetch_all(MYSQLI_ASSOC);


        $res->close();

        $this->lastSql=$sql;

        return $arr;


    }


    /**
     * ['yy'=>123,'kk'=>456]
     *
     *
     * 添加
     * Create by Peter
     * @param $data
     * @return bool|mixed
     * @throws Exception
     */
    function add($data){


        $option=$this->option;



        $this->init_options();


        if(!$data) return false;

        if(!is_array($data)) return false;

        $key=array_keys($data);


        $key=array_map(function ($v){


            return "`".$v."`";

        },$key);

        $fields=join(',',$key);

        $values=array_values($data);

        $values=array_map(function ($v){

            return "'".$v."'";


        },$values);


        $d=join(',',$values);



        $sql="insert into {$option['table']} ({$fields})  VALUE  ({$d})";


        $res=$this->DbObj->query($sql);


        if($this->DbObj->error) throw new \mysqli_sql_exception($this->DbObj->error.",ERROR SQL:".$sql);

        $this->lastSql=$sql;

       return $this->DbObj->insert_id;


    }


    /**
     *
     * ['yy'=>123,'kk'=>456]
     *
     *
     *
     * 更新
     * Create by Peter
     * @param $data
     * @return bool|\mysqli_result
     * @throws Exception
     */
    function update($data){


        $option=$this->option;

        $this->init_options();

        if(!$data) return false;


        if(!is_array($data)) return false;

        $where="";


        if(!$option['where']) throw new \mysqli_sql_exception('where条件不存在！');


        if($option['where']) $where="where ".$option['where'];





        $change="";

        foreach ($data as $key=>$value){


            $change.='`'.$key.'`'."="."'".$value."',";


        }


        $change=substr($change,0,strlen($change)-1);

//        echo $change;


        $sql="update {$option['table']} set {$change} {$where}  limit 3";




        $res=$this->DbObj->query($sql);


        if($this->DbObj->error) throw new \mysqli_sql_exception($this->DbObj->error.",ERROR SQL:".$sql);

        $this->lastSql=$sql;



        return $this->DbObj->affected_rows;






    }


    /**
     *
     * 字段自增
     * Create by Peter
     * @param $field
     * @param $num
     * @return bool|\mysqli_result
     * @throws Exception
     */
    function NumZ($field,$num){


        $option=$this->option;

        $this->init_options();


        $where="";


        if(!$option['where']) throw new \mysqli_sql_exception('where条件不存在！');


        if($option['where']) $where="where ".$option['where'];




        $sql="update {$option['table']} set `{$field}`= `{$field}`+ {$num} {$where}  limit 3";




        $res=$this->DbObj->query($sql);



        if($this->DbObj->error) throw new \mysqli_sql_exception($this->DbObj->error.",ERROR SQL:".$sql);

        $this->lastSql=$sql;

        return $this->DbObj->affected_rows;







    }



    /**
     * 表名
     * Create by Peter
     * @param $tableName
     * @return $this
     */
    function table($tableName){


        $this->option['table']="`".$tableName."`";

        return $this;
    }

    /**
     * 条件
     * Create by Peter
     * @param $where
     * @return $this
     */
    function where($where){


        $this->option['where']=$where;

        return $this;

    }

    /**
     * 获取长度
     * Create by Peter
     * @param $offset
     * @param int $length
     * @return $this
     */
    function limit($offset,$length=0){


        $this->option['limit']=($length==0)?"{$offset}":"{$offset},{$length}";


        return $this;

    }

    /**
     * 排序
     * Create by Peter
     * @param $field
     * @param string $sort
     * @return $this
     */
    function order($field,$sort='asc'){



//        $this->

        $f_arr=explode(".",$field);


//        print_r($f_arr);


        $f_arr=array_map(function ($v){

            return "`".$v."`";

        },$f_arr);


        $field=join('.',$f_arr);


        $this->option['order']=$field." ".$sort;

        return $this;


    }

    /**
     * 分组
     * Create by Peter
     * @param $field
     * @return $this
     */
    function group($field){


        $f_arr=explode('.',$field);


        $f_arr=array_map(function ($v){

            return "`".$v."`";

        },$f_arr);


        $field=join('.',$f_arr);



        $this->option['group']=$field;




        return $this;

    }


    function fields($fields){


//        $this->option['fields']=$fields;


        $f_arr=explode(',',$fields);


        $f_arr=array_map(function ($v){



            $f_arr=explode('.',$v);


            $f_arr=array_map(function ($v){

                return "`".$v."`";

            },$f_arr);


            $field=join('.',$f_arr);


            return $field;

        },$f_arr);


        $fields=join(',',$f_arr);


        $this->option['fields']=$fields;


        return $this;

    }


    function join($table,$condition,$type="left"){






        $this->option['join'].=" {$type} join `{$table}` on {$condition}";


        return $this;
    }


    /**
     * 初始化参数
     * Create by Peter
     */
    function init_options()
    {


        foreach ($this->option as $key => $value) {


            if (is_array($value)) {

                $this->option[$key] = [];

            } elseif (is_numeric($value)) {

                $this->option[$key] = 0;

            } else {

                $this->option[$key] = "";
            }

        }

    }


    /**
     * 关闭数据库连接
     * Create by Peter
     */
    function close(){


        if (!$this->DbObj->connect_errno) {

            $this->DbObj->close();
        }

    }

    /**
     * 开启一个事务
     * Create by Peter
     */
    function begin(){

        $this->DbObj->begin_transaction();

    }


    /**
     * 提交
     * Create by Peter
     */
    function commit(){


        $this->DbObj->commit();
    }


    /**
     * 回滚
     * Create by Peter
     */
    function rollback(){


        $this->DbObj->rollback();

    }



    function getLastSql(){


        return $this->lastSql;

    }






}