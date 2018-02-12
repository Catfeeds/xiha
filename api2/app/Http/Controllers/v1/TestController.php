<?php

/**
 * 测试模块
 * lumen查询构造器 https://laravel-china.org/docs/5.3/queries
 * @return void
 * @author 
 **/

namespace App\Http\Controllers\v1;

use Exception;
use App\Models\v1\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignupController extends Controller {
	
	protected $request;
	
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	// 	获取教练名片
	    public function getCoachDetail() {
		var_dump($this->request->input());
		
		if(!$this->request->has('param')) {
			return response()->json([
			                'code'=>103, 
			                'msg'=>'参数错误',
			                'data'=>'',
			            ]);
		}
		// 		从数据表中获取所有的数据列
		        $test = DB::table('test')->get();
		
		// 		从数据表中获取单个列或行
		        $test = DB::table('test')->where('name', 'john')->first();
		
		// 		则可以使用 value 方法来从单条记录中取出单个值。
		        $test = DB::table('test')->where('name', 'john')->value('email');
		
		// 		获取一列的值 你也可以在返回的数组中指定自定义的键值字段
		        $test = DB::table('test')->pluck('email');
		
		// 		获取一列的值 你也可以在返回的数组中指定自定义的键值字段
		        $test = DB::table('test')->pluck('email', 'name');
		
		// 		结果分块 让我们将整个 users 数据表进行分块，每次处理 100 条记录
		        DB::table('users')->orderBy('id')->chunk(100, function($users) {
			foreach ($users as $user) {
				//
			}
		}
		);
		
		// 		聚合 count, max, min, avg, sum
		        $users = DB::table('users')->count();
		$price = DB::table('orders')->max('price');
		$price = DB::table('orders')
		                ->where('finalized', 1)
		                ->avg('price');
		
		// 		指定一个 Select 子句 你并不会总是想从数据表中选出所有的字段。这时可以使用 select 方法为查找指定一个自定义的 select 子句：
		        $users = DB::table('users')->select('name', 'email as user_email')->get();
		
		// 		distinct 方法允许你强制让查找返回不重复的结果：
		        $users = DB::table('users')->distinct()->get();
		
		// 		若你已有一个查询构造器实例，且希望在其现存的 select 子句中加入一个字段，则可以使用 addSelect 方法：
		        $query = DB::table('users')->select('name');
		$users = $query->addSelect('age')->get();
		
		// 		原始表达式
		        // 		有时你可能需要在查询中使用原始表达式。这些表达式会被当作字符串注入到查找中，因此要小心避免造成数据库注入攻击！要创建一个原始表达式，可以使用 DB::raw 方法：
		        $users = DB::table('users')
		                 ->select(DB::raw('count(*) as user_count, status'))
		                 ->where('status', '<>', 1)
		                 ->groupBy('status')
		                 ->get();
		
		// 		Inner Join 语法
		        $users = DB::table('users')
		                ->join('contacts', 'users.id', '=', 'contacts.user_id')
		                ->join('orders', 'users.id', '=', 'orders.user_id')
		                ->select('users.*', 'contacts.phone', 'orders.price')
		                ->get();
		
		// 		Left Join 语法#
		        $users = DB::table('users')
		                ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
		                ->get();
		
		// 		Cross Join 语法 使用 crossJoin 方法和你想要交叉连接的表名来做「交叉连接」。交叉连接通过第一个表和连接表生成一个笛卡尔积：
		        $users = DB::table('sizes')
		            ->crossJoin('colours')
		            ->get();
		
		// 		高级的 Join 语法
		        DB::table('users')
		            ->join('contacts', function ($join) {
			// $join->on('users.id', '=', 'contacts.user_id')->orOn(...);
		}
		)
		            ->get();
		
		DB::table('users')
		            ->join('contacts', function ($join) {
			$join->on('users.id', '=', 'contacts.user_id')
			                     ->where('contacts.user_id', '>', 5);
		}
		)
		            ->get();
		
		// 		Unions
		        // 		查询语句构造器也提供了一个快捷的方法来「合并」两个查找。例如，你可以先创建一个初始查找，然后使用 union 方法将它与第二个查找进行合并：
		        $first = DB::table('users')
		            ->whereNull('first_name');
		
		$users = DB::table('users')
		                    ->whereNull('last_name')
		                    ->union($first)
		                    ->get();
		
		// 		Where 子句
		        $users = DB::table('users')->where('votes', '=', 100)->get();
		$users = DB::table('users')
		                        ->where('votes', '>=', 100)
		                        ->get();
		
		$users = DB::table('users')
		                        ->where('votes', '<>', 100)
		                        ->get();
		
		$users = DB::table('users')
		                        ->where('name', 'like', 'T%')
		                        ->get();
		$users = DB::table('users')->where([
		            ['status', '=', '1'],
		            ['subscribed', '<>', '1'],
		        ])->get();
		
		// 		若你只想简单地验证某个字段等于一个指定的值，则可以直接将这个值作为第二个参数传入 where 方法：
		        $users = DB::table('users')->where('votes', 100)->get();
		
		// 		Or 语法
		        $users = DB::table('users')
		                        ->where('votes', '>', 100)
		                        ->orWhere('name', 'John')
		                        ->get();
		
		// 		whereBetween
		        $users = DB::table('users')
		                    ->whereBetween('votes', [1, 100])->get();
		
		// 		whereNotBetween
		        $users = DB::table('users')
		                    ->whereNotBetween('votes', [1, 100])
		                    ->get();
		
		// 		whereIn 与 whereNotIn
		        $users = DB::table('users')
		                    ->whereNotIn('id', [1, 2, 3])
		                    ->get();
		
		// 		whereNull 与 whereNotNull 
		        // 		whereNull 方法验证指定列的值为 NULL：
		        $users = DB::table('users')
		                    ->whereNull('updated_at')
		                    ->get();
		
		// 		whereNotNull 方法验证一个列的值不为 NULL：
		        $users = DB::table('users')
		                    ->whereNotNull('updated_at')
		                    ->get();
		
		// 		whereDate / whereMonth / whereDay / whereYear
		        $users = DB::table('users')
		                ->whereDate('created_at', '2016-10-10')
		                ->get();
		$users = DB::table('users')
		                ->whereMonth('created_at', '10')
		                ->get();
		$users = DB::table('users')
		                ->whereDay('created_at', '10')
		                ->get();
		$users = DB::table('users')
		                ->whereYear('created_at', '2016')
		                ->get();
		
		// 		whereColumn 用来检测两个列的数据是否一致：
		        $users = DB::table('users')
		                ->whereColumn('first_name', 'last_name')
		                ->get();
		
		$users = DB::table('users')
		                ->whereColumn('updated_at', '>', 'created_at')
		                ->get();
		// 		可以接受数组传参，条件语句会使用 and 连接起来：
		        $users = DB::table('users')
		                ->whereColumn([
		                    ['first_name', '=', 'last_name'],
		                    ['updated_at', '>', 'created_at']
		                ])->get();
		
		// 		参数分组
		        $users = DB::table('users')
		            ->where('name', '=', 'John')
		            ->orWhere(function ($query) {
			$query->where('votes', '>', 100)
			                      ->where('title', '<>', 'Admin');
		}
		)
		            ->get();
		// 		生成sql: select * from users where name = 'John' or (votes > 100 and title <> 'Admin')
		
		// 		Where Exists 语法
		        DB::table('users')
		            ->whereExists(function ($query) {
			$query->select(DB::raw(1))
			                      ->from('orders')
			                      ->whereRaw('orders.user_id = users.id');
		}
		)
		            ->get();
		// 		生成sql: select * from users where exists (select 1 from orders where orders.user_id = users.id)
		
		// 		JSON 查询语句
		        $users = DB::table('users')
		                        ->where('options->language', 'en')
		                        ->get();
		
		$users = DB::table('users')
		                        ->where('preferences->dining->meal', 'salad')
		                        ->get();
		
		// 		Ordering, Grouping, Limit 及 Offset#
		        // 		orderBy
		        $users = DB::table('users')
		                ->orderBy('name', 'desc')
		                ->get();
		// 		inRandomOrder# 会对数据结果进行随机排序，例如以下读取随机用户：
		        $randomUser = DB::table('users')
		                ->inRandomOrder()
		                ->first();
		
		// 		groupBy / having / havingRaw#
		        $users = DB::table('users')
		                ->groupBy('account_id')
		                ->having('account_id', '>', 100)
		                ->get();
		
		// 		havingRaw 方法可用来将原始字符串设置为 having 子句的值。
		        $users = DB::table('orders')
		                ->select('department', DB::raw('SUM(price) as total_sales'))
		                ->groupBy('department')
		                ->havingRaw('SUM(price) > 2500')
		                ->get();
		
		// 		skip / take 要限制查找所返回的结果数量，或略过指定数量的查找结果（偏移），
		        $users = DB::table('users')->skip(10)->take(5)->get();
		
		// 		条件查询语句 
		        // 		有时候，你希望某个值为 true 的时候才执行查询，例如，如果一个请求中存在给定的输入值的时候才执行这个 where 语句，你可以使用 when 方法实现：
		        $role = $request->input('role');
		// 		只有当 when 的第一个参数为 true 的话，匿名函数里的 where 语句才会被执行。如果第一个参数是 false 闭包将不会被执行。
		        $users = DB::table('users')
		                        ->when($role, function ($query) use ($role) {
			return $query->where('role_id', $role);
		}
		)
		                        ->get();
		
		// 		Inserts
		        DB::table('users')->insert(
		            ['email' => 'john@example.com', 'votes' => 0]
		        );
		
		DB::table('users')->insert([
		            ['email' => 'taylor@example.com', 'votes' => 0],
		            ['email' => 'dayle@example.com', 'votes' => 0]
		        ]);
		
		// 		自动递增 ID insertGetId 方法来插入记录并获取其 ID：
		        // 		当使用 PostgreSQL 时，insertGetId 方法将预测自动递增字段的名称为 id。若你要从不同「顺序」来获取 ID，则可以将顺序名称作为第二个参数传给 insertGetId 方法。
		        $id = DB::table('users')->insertGetId(
		            ['email' => 'john@example.com', 'votes' => 0]
		        );
		
		// 		Updates
		        DB::table('users')
		                    ->where('id', 1)
		                    ->update(['votes' => 1]);
		
		// 		Updating JSON Columns
		        DB::table('users')
		            ->where('id', 1)
		            ->update(['options->enabled' => true]);
		
		// 		递增或递减
		        // 		两个方法都必须接收至少一个参数（要修改的字段）。也可选择性地传入第二个参数，用来控制字段应递增／递减的量：
		        DB::table('users')->increment('votes');
		DB::table('users')->increment('votes', 5);
		
		DB::table('users')->decrement('votes');
		DB::table('users')->decrement('votes', 5);
		
		// 		也可指定要在操作中更新其他字段
		        DB::table('users')->increment('votes', 1, ['name' => 'John']);
		
		// 		Deletes
		        DB::table('users')->delete();
		DB::table('users')->where('votes', '>', 100)->delete();
		
		// 		若你希望截去整个数据表的所有数据列，并将自动递增 ID 重设为零，则可以使用 truncate 方法：
		        DB::table('users')->truncate();
		
		// 		悲观锁定
		        // 		查询语句构造器也包含一些可用以协助你在 select 语法上作「悲观锁定」的函数。若要以「共享锁」来运行语句，则可在查找上使用 sharedLock 方法。共享锁可避免选择的数据列被更改，直到事务被提交为止：
		
		DB::table('users')->where('votes', '>', 100)->sharedLock()->get();
		
		// 		也可以使用 lockForUpdate 方法。「用以更新」锁可避免数据列被其它共享锁修改或选取：
		        DB::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
		
	}
}
?>