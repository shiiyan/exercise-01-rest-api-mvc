<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class IndexController extends Controller
{
    public function indexAction()
    {
        echo '<h1>Hello!</h1>';
    }

    public function getallAction()
    {
    	//echo '<h1>Test!</h1>';

		$products = Products::find();

		echo json_encode($products, JSON_UNESCAPED_SLASHES);
    }

    public function getbynameAction()
    {
    	$name = $this->dispatcher->getParam("name");
    	$products = Products::find(
    		[
    			'conditions' => "name LIKE ?1",
    			'bind' => [
    				1 => "%$name%"
    			]
    		]
    	);
    	if ($products->valid() === false) {
			echo json_encode(['status' => 'NOT-FOUND']);
		} else {
			echo json_encode($products, JSON_UNESCAPED_SLASHES);
		}
    }

    public function getbyidAction()
    {
    	$id = $this->dispatcher->getParam("id");
		$product = Products::findFirst($id);
		if ($product === false) {
			echo json_encode(['status' => 'NOT-FOUND']);
		} else {
			echo json_encode($product, JSON_UNESCAPED_SLASHES);
		}
    }

    private function getResponses($success, $product) {
        // Create a response
        $response = new Response();

        // Check if the insertion was successful
        if ($success) {
            // Change the HTTP status
            $response->setStatusCode(201, 'Created');

            //$product->id= $status->getModel()->id;

            
            $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data' => $product

                ], JSON_UNESCAPED_SLASHES
            );
        } else {
            // Change the HTTP status
            $response->setStatusCode(409, 'Conflict');

            // Send errors to the client
            $errors = [];

            foreach($product->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status' => 'ERROR',
                    'messages' => $errors
                ]
            );
        }
        return $response;
    }

    public function addAction()
    {
    	$product = new Products();


		if ($this->request->hasFiles()) {
			$file = $this->request->getUploadedFiles()[0];
			$file_name = $file->getName();
			$file->moveTo(BASE_PATH.'/public/uploads/'.$file_name);
		} else {
			return json_encode(['status' => 'NO-FILE']);
		}

		$product->image_url = '/public/uploads/'.$file_name;

		$success = $product->save(
    		$this->request->getPost()
    	);

		//echo gettype($product);
		//print_r($product);
		
        return $this->getResponses($success, $product);

    }

    public function updatebyidAction()
    {
    	$product = new Products();
    	$id = $this->dispatcher->getParam("id");
    	$product_origin = Products::findFirst($id);

    	$product_new = $this->request->getJsonRawBody();

		$success = $product->save(
			[
				'id'   => $id,
                'name' => property_exists($product_new, 'name')?$product_new->name:$product_origin->name,
                'detail' => property_exists($product_new, 'detail')?$product_new->detail:$product_origin->detail,
                'price' => property_exists($product_new, 'price')?floatval($product_new->price):$product_origin->price,
                'image_url' => property_exists($product_new, 'image_url')?$product_new->image_url:$product_origin->image_url,
			]
    	);

        return $this->getResponses($success, $product);
    }

       public function deletebyidAction()
    {
    	$id = $this->dispatcher->getParam("id");
		$product = Products::findFirst($id);
		if ($product->delete() === false) {
			echo json_encode(['status' => 'NOT-FOUND']);
		} else {
			echo json_encode(['status' => 'DELETED']);
		}
    }

}
