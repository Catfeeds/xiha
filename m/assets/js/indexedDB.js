var cityData = [
	{city:"Redmond", population:57530},
	{city:"Atlanta",population:447841},
	{city:"San Fracisco", population:837442}
];

// Create IndexdDB database and fill it with data from array
alasql('CREATE INDEXEDDB DATABASE IF NOT EXISTS geo;\
    ATTACH INDEXEDDB DATABASE geo; \
    USE geo; \
    DROP TABLE IF EXISTS cities; \
    CREATE TABLE cities; \
    SELECT * INTO cities FROM ?', [cityData], function(){

    // Select data from IndexedDB
	alasql('SELECT COLUMN city FROM cities WHERE population > 500000 ORDER BY city DESC',
		[],function(res){
			console.log(res.join(', '));
	});
});
