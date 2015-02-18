# Dygraphs Widget for Yii
-------------------------
A simple graph widget for Yii 2, based on [Dygraphs] (http://dygraphs.com/).

## Installation
---------------
Download the latest release and unpack the **contents** of the `widget` folder inside the `protected\extensions\dygraphswidget` folder within your Yii application.

## Usage
--------
In your view, create the widget with your data matrix as its *data* option.
```php
$this->widget('ext.dygraphswidget.DygraphsWidget', array(
		'data'=> $your_data,
	));
```

## Dygraphs options
-------------------
You can set the *options* property to pass additional options to the Dygraphs object:
```php
$this->widget('DygraphsWidget', array(
		'data'=> $your_data,
		'options'=>array(
			'labels' => array('X', 'Sin', 'Rand', 'Pow'),
			'title'=> 'Main Graph',
			//...
		),
	));
```

## Data formats
---------------
The data property can be specified in three different formats. Consider the following examples, and make sure to read [the official documentation] (http://dygraphs.com/data.html) for more details:
- **Matrix**
```php
$data = array(
	array(1, 25, 100),
	array(2, 50, 90),
	array(3, 100, 80),
	//...
);
```
- **URL**
URL to a text file with the data.
```php
$data = 'http://dygraphs.com/dow.txt';
```
- **JavaScript**
JS code that returns a data object usable by Dygraphs. The code must be wrapped inside a JsExpression object:
```php
$data = new JsExpression('function () {
	var data = [];
      for (var i = 0; i < 1000; i++) {
        var base = 10 * Math.sin(i / 90.0);
        data.push([i, base, base + Math.sin(i / 2.0)]);
      }
      var highlight_start = 450;
      var highlight_end = 500;
      for (var i = highlight_start; i <= highlight_end; i++) {
        data[i][2] += 5.0;
      }
	return data;
}');
```

## Additional options
---------------------
The following widget properties can also be specified:
- **xIsDate**: Set this property to true if the x-values (first value in each row of the data matrix) are date strings, in order to properly convert them to JS date objects for Dygraphs.
- **scriptUrl**: The URL where the Dygraphs.js library is taken from. If not set, the widget will locally publish its own distribution of the Dygraphs library.
- **model** and **attribute**: Specify a CModel instance and one of its attributes in order to take the data from it.
- **jsVarName**: Specifies a custom name for the JS variable that will receive the Dygraphs object upon creation.
- **htmlOptions**: Additional HTML attributes for the graph-containing div.

## JavaScript code in options
-----------------------------
Anytime you need to pass JavaScript code (for example, passing a function to an option), just pass a new JsExpression($your_js_code). For example:
```php
$options = array(
    'underlayCallback' => new JsExpression('function(canvas, area, g)
            {
                var bottom_left = g.toDomCoords(highlight_start, -20);
                var top_right = g.toDomCoords(highlight_end, +20);
 
                var left = bottom_left[0];
                var right = top_right[0];
 
                canvas.fillStyle = "rgba(255, 255, 102, 1.0)";
                canvas.fillRect(left, area.y, right - left, area.h);
            }'),
);
```

 