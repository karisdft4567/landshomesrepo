<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


   
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
    
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'type'=>'required|integer|between:1,3',
            'image' => 'required|image|max:2048',

        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
       // $product = Product::create($input);

        
   // $imagePath = $request->file('image')->store('public/images');
    $imageName = time().'.'.$request->image->extension();  
     
    $request->image->move(public_path('images'), $imageName);
    $baseUrl = URL::to('/');

    $product = new Product([
        'name' => $request->get('name'),
        'description' => $request->get('description'),
        'type'=>$request->get('type'),
        'image' => $baseUrl.'/images/'.$imageName,
    ]);
    $product->save();
   
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'type'=>'required|integer|between:1,3',

           // 'image' => 'required|image|max:2048',

        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        if(isset($request->image) && $request->image="") {
            $imageName = time().'.'.$request->image->extension();  
            
            $request->image->move(public_path('images'), $imageName);
            $baseUrl = URL::to('/');

            
            $product->name = $request->get('name');
            $product->description = $request->get('description');
            $product->type = $request->get('type');
            $product->image = $baseUrl.'/images/'.$imageName;
            
            $product->save();
        } else {

            $product->name = $request->get('name');
            $product->description = $request->get('description');
            $product->type = $request->get('type');
            $product->save();

        }
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }

    /**
     * Remove data older than 30 days.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroypast(): JsonResponse
    { 
        //$product->delete();
        Product::whereDate( 'created_at', '<=', now()->subDays(30))->delete();


   
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}