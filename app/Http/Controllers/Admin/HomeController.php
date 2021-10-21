<?php

namespace App\Http\Controllers\Admin;

Use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Visitor;
use App\Page;
use App\User;

class HomeController extends Controller
{

    public function __construct () {
       $this->middleware('auth');
    }
    
    public function index (Request $request) {

        $visitsCount = 0;
        $onlineCount = 0;
        $pageCount = 0;
        $userCount = 0;
        $interval = intval($request->input('interval', 30));
        if($interval >120) {
            $interval = 120;
        }

        //Contagem de visitantes
        $dateInterval = date('Y-m-d H-i-s', strtotime('-'.$interval.' days'));
        $visitsCount = Visitor::where('date_acess', '>=', $dateInterval)->count();

        //Contagem de usuários online
        $datelimit = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $onlineList = Visitor::select('ip')->where('date_acess', '>=', $datelimit)->groupBy('ip')->get();
        $onlineCount = count($onlineList);



        // Contagem de páginas 
        $pageCount = Page::count();

        //Contagem de Usuários 
        $userCount = User::count();

    // Contagem para o pagepie
          $visitsAll = Visitor::selectRaw('page, count(page) as c')->where('date_acess', '>=', $dateInterval)->groupBy('page')->get();
          foreach($visitsAll as $visit) {
              $pagePie[ $visit['page'] ] = intval($visit['c']);
          }

        $pageLabels = json_encode( array_keys($pagePie) );
        $pageValues = json_encode( array_values($pagePie) );

        return view('admin.home', [
            'visitsCount' => $visitsCount,
            'onlineCount' => $onlineCount,
            'pageCount' => $pageCount,
            'userCount' => $userCount,
            'pageLabels' => $pageLabels,
            'pageValues' => $pageValues,
            'dateInterval' => $interval
        ]);
    }
}
