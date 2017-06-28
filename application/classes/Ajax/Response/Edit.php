<?php
class Ajax_Response_Edit {
	
	private function getChildrens($parentId){
		// load the data model class
		$module = new Table_Modules();
		$childrens = $module -> selectModules( $where = array(
													"parent_id" => array( "=" => $parentId ), 
													"id" => array (">" => 1)
												  	), $order = "display_order"
											);		
		$json = '['; $i=0; // json start
		foreach($childrens as $child){ // browse the childrens one by one
			$i++;
			// generating json
			$json .= '{"title": "'.$child->name_en.'", "key": "'.$child->id.'" , "form": "'.$child->form_name.'"';
			//count the number of childrens for this child
			$hasChilds = $module->selectModules($where = array(
												"parent_id" => array("=" => $child->id)
												));		
			//if there are any childrens, make a recoursive call
			if( $hasChilds->count() ){
				$json .= ', "isFolder": true, ';
				$json .= '"children":'. $this->getChildrens($child->id);
			}
			$json .= '}';
			//if this is not the last row, add a comma
			$json .= ( $i<( $childrens->count() ) )?",":"";	
		}
		$json .= ']'; // json end
		
		return $json;
	}
	
	public function loadTreeJson($par = 1){
		
		echo $this->getChildrens($par);
	}
	
	public function fillChildValues($text){
		$json['submenus'] = "";
		
		$modules = new Table_Modules();
		
		$foundModule = $modules->selectModules($where = array(
							"name_en" => array("=" => $text)
							));
							
		if($foundModule->count() == 1){
			
			$subModules = $modules->getSubModules($foundModule->current()->id);
		
			$json['submenus'] = $subModules->toArray();	

		}

		echo Utility_Functions::_toJson($json);
	}
	
	public function Blank(){
		
	}
	public function PlaceHolder(){
	
	}
	
	//Receipts Data
	public function Receipts(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Ingredients Data
	public function ConfigIngredients(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//Kategorite e Recetave
	public function ConfigReceiptCategory(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Llojet e Kuzhines
	public function ConfigReceiptCuisineType(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Llojet e Recetave
	public function ConfigReceiptType(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Vaktet
	public function ConfigReceiptMeal(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Sezonaliteti
	public function ConfigReceiptSeasonality(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Produkti Baze
	public function ConfigReceiptBaseProduct(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Festat
	public function ConfigReceiptFestivity(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Kategorite e Perberesve
	public function ConfigIngredientsCategory(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//Sektoret e Blogut
	public function BlogSections(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Kategorite e Blogut
	public function BlogCategories(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Autoret
	public function Authors(Array $parameters, $action="default"){		
		$className = __CLASS__.'_'.__FUNCTION__;	
		$moduleController = new  $className ($parameters, $action);
	}
	
	//Perberesit e recetes
	public function ReceiptsIngredients(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//Hapat e recetes
	public function ReceiptsSteps(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Galeria e Recetes
	public function ReceiptPhotoGallery(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//parking Types Configuration Data
	public function ConfigParkingTypes(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//Home Page Featured Articles and Receipts
	public function WebFeatured(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Home Page Sponsored Articles and Receipts
	public function WebSponsored(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Daily Recipes
	public function WebDailyRecipes(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Recipes From Web
	public function RecipesFromWeb(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Comment
	public function Comments(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	// ******  Begin Blog Data  ******//
	
	public function BlogArticles(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	//Galeria e Artikuje
	public function ArticlePhotoGallery(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	// ******  Begin Security Data  ******//
	
	public function Role(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	
	public function Users(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function Acl(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function AclArticleCategories(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	public function AclReceiptCategories(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	// ******  Begin Stats Data  ******//
	public function RptTotalsUsersByCategory(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	public function RptArticleTotalsByDate(Array $parameters, $action="default"){
	    $className = __CLASS__.'_'.__FUNCTION__;
	    $moduleController = new  $className ($parameters, $action);
	}
	
	// ******  End Stats Data  ******//
	
}//end class

