<?php
namespace sibilino\y2dygraphs;

use yii\base\Model;
use PHPUnit_Framework_TestCase;
use yii\helpers\VarDumper;
use yii\web\View;
use yii\web\JsExpression;

class TestModel extends Model 
{
	public $chart = [
		[1, 25, 100],
		[2, 50, 90],
		[3, 100, 80],
	];
}

class DygraphsWidgetTest extends PHPUnit_Framework_TestCase {
	/* @var $widget DygraphsWidget */
	public function testInit() {
		
		$model = new TestModel();
		$widget = DygraphsWidget::begin([
			'model' => $model,
			'attribute' => 'chart',
		]);
		
		$this->assertInstanceOf('sibilino\y2dygraphs\DygraphsWidget', $widget);
		$this->assertTrue(isset($widget->htmlOptions['id']));
		$this->assertTrue(isset($widget->jsVarName));
		$this->assertEquals($model->chart, $widget->data);
		$this->assertArrayHasKey('sibilino\y2dygraphs\DygraphsAsset', $widget->view->assetBundles);
	}
	
	public function testRun() {
		$this->assertEquals('<div id="test"></div>',DygraphsWidget::widget([
			'htmlOptions' => ['id' => 'test'],
		]));
	}
	
	public function testDataUrl() {
		$widget = DygraphsWidget::begin([
			'data' => 'http://localhost/testdata.csv',
		]);
		$widget->end();
		$this->assertContains('"http://localhost/testdata.csv",', $this->getLastScript($widget));
	}
	
	public function testDataFunction() {
		$widget = DygraphsWidget::begin([
			'data' => new JsExpression('function () { return [0, 7, 21]; }'),
		]);
		$widget->end();
		$this->assertContains('function () { return [0, 7, 21]; },', $this->getLastScript($widget));
	}
	
	public function testDataArray() {
		$widget = DygraphsWidget::begin([
			'data' => [
				[1,25,100],
				[2,50,90],
				[3,100,80]
			],
		]);
		$widget->end();
		$this->assertContains('[[1,25,100],[2,50,90],[3,100,80]],', $this->getLastScript($widget));
	}
	
	public function testDataWithDates() {
		$widget = DygraphsWidget::begin([
			'data' => [
				["2014/01/10 00:06:50", 25, 100],
				["2014/12/23 10:16:40", 50, 90],
				["2015/07/01 03:09:19", 100, 80]
			],
			'xIsDate' => true,
		]);
		$widget->end();
		$this->assertContains(
			"[[new Date('2014/01/10 00:06:50'),25,100],[new Date('2014/12/23 10:16:40'),50,90],[new Date('2015/07/01 03:09:19'),100,80]],",
			$this->getLastScript($widget));
	}
	
	public function testVarName() {
		$widget = DygraphsWidget::begin([
			'jsVarName'=>'testvar',
		]);
		$widget->end();
		$this->assertContains("var testvar = new Dygraph(", $this->getLastScript($widget));
	}
	
	public function testOptions() {
		$widget = DygraphsWidget::begin([
			'options' => [
                'strokeWidth' => 2,
                'parabola' => [
                  'strokeWidth' => 0.0,
                  'drawPoints' => true,
                  'pointSize' => 4,
                  'highlightCircleSize' => 6
                ],
                'line' => [
                  'strokeWidth' => 1.0,
                  'drawPoints' => true,
                  'pointSize' => 1.5
                ],
                'sine wave' => [
                  'strokeWidth' => 3,
                  'highlightCircleSize' => 10
                ],
			],
		]);
		$widget->end();
		$this->assertContains(
				'{"strokeWidth":2,"parabola":{"strokeWidth":0,"drawPoints":true,"pointSize":4,"highlightCircleSize":6},"line":{"strokeWidth":1,"drawPoints":true,"pointSize":1.5},"sine wave":{"strokeWidth":3,"highlightCircleSize":10}}',
				$this->getLastScript($widget));
	}
	
	public function testHtmlOptions() {
		$output = DygraphsWidget::widget([
			'htmlOptions' => [
				'id' =>  'test-id',
				'class' => 'test-class centered',
				'data-toggle' => 'dropdown',
				'onChange' => "alert('hello')"
			],
		]);
		$this->assertEquals(
				'<div id="test-id" class="test-class centered" data-toggle="dropdown" onChange="alert(&#039;hello&#039;)"></div>',
				$output);
	}
	
	/**
	 * @param DygraphsWidget $widget
	 * @return string
	 */
	private function getLastScript($widget) {
		$scripts = $widget->view->js[View::POS_READY];
		return end($scripts);
	}
}