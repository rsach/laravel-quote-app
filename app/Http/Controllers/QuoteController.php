<?php 
namespace App\Http\Controllers;

use App\Author;
use App\Qoute;
use App\AuthorLog;
use Illuminate\Http\Request;
use App\Events\QuoteCreated; 
use Illuminate\Support\Facades\Event;

class QuoteController extends Controller{
    
    public function getIndex($author = null){
        
        
        if(!is_null($author)){
            $quote_author= Author::where('name',$author)->first();
            if($quote_author){
                $quotes = $quote_author->quotes()->orderBy('created_at','desc')->paginate(6);    
                
            }
            
        }else{
            
        $quotes=Qoute::orderBy('created_at','desc')->paginate(6);
        
        }
        return view('index' ,['quotes'=>$quotes]);
        
    }
    
    public function postQuote(Request $request){
        
        $this->validate($request,[
                'author' => 'required|max:60|alpha',
                'quote' => 'required|max:500'
            ]);
        $authorText = ucfirst($request['author']);
        $authorEmail = $request['email'];
        $quoteText = $request['quote'];
        
        $author = Author::where('name',$authorText)->first();
        if(!$author){
            $author = new Author();
            $author->name = $authorText; 
            $author->email = $authorEmail;
            $author->save();
            
        }
        
        $quote = new Qoute();
        $quote->quote =$quoteText;
        $author->quotes()->save($quote);
        
        Event::fire(new QuoteCreated($author));
        
        return redirect()->route('index')->with([
            'success' => 'Quote Saved! '
            ]);
    }
    
    public function getDeleteQuote($quote_id){
          $quote = Qoute::find($quote_id);
          $flag = false;
          
          if(count($quote->author->quotes) === 1){
              $quote->author->delete();
              $flag=true;
              
          }
          $quote->delete();
          
          $msg = $flag ? 'Author and quote deleted' : 'Quote Deleted';
          
            return redirect()->route('index')->with(['success' => $msg]);
    }
    
    
    public function getMailCallback($author_name){
        
        
        $author_log = new AuthorLog();
        $author_log->author = $author_name;
        $author_log->save();
        
        
        return view('callback',[
                'author' => $author_name
            ]);
        
    }
}
