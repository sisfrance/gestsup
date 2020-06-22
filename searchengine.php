<?php
################################################################################
# @Name : searchengine.php
# @Desc : search engine in database tickets
# @call : /dashboard.php
# @paramters : keywords
# @Autor : Flox
# @Create : 12/01/2011
# @Update : 03/09/2013
# @Version : 2.9
################################################################################

//initialize Session variables
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';

//case when keywords contain '
$keywords = str_replace("'","\'",$keywords);

//keywords table space separation
$keyword=explode(" ",$keywords);

//count $keywords
$nbkeyword= sizeof($keyword);

if ($nbkeyword==2)
{
	$from = "
		FROM tincidents, tstates, tthreads 
		WHERE
		tincidents.state=tstates.id AND
		tincidents.id=tthreads.ticket AND
		(title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.id = '$keyword[0]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[0]%' or lastname LIKE '%$keyword[0]%'))
		AND
		(tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.id LIKE '$keyword[1]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[1]%' or lastname LIKE '%$keyword[1]%'))
		AND disable='0'
	"; 
}
else if ($nbkeyword==3)
{
	$from = "
		FROM tincidents, tstates, tthreads 
		WHERE
		tincidents.state=tstates.id AND
		tincidents.id=tthreads.ticket AND
		(tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.id = '$keyword[0]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[0]%' or lastname LIKE '%$keyword[0]%'))
		AND
		(tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.id LIKE '$keyword[1]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[1]%' or lastname LIKE '%$keyword[1]%'))
		AND
		(tincidents.title LIKE '%$keyword[2]%' OR 
		tincidents.description LIKE '%$keyword[2]%' OR 
		tthreads.text LIKE '%$keyword[2]%' OR
		tincidents.id LIKE '$keyword[2]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[2]%' or lastname LIKE '%$keyword[2]%'))
		AND disable='0'
	"; 
}

else
{
$from = "
	FROM  tincidents, tcategory, tsubcat, tstates, tthreads 
	WHERE
	tincidents.state=tstates.id AND
	tincidents.subcat=tsubcat.id AND
	tincidents.category=tcategory.id AND
	tincidents.id=tthreads.ticket AND
	(
	tincidents.title LIKE '%$keyword[0]%' OR 
	tincidents.description LIKE '%$keyword[0]%' OR 
	tthreads.text LIKE '%$keyword[0]%' OR
	tsubcat.name LIKE '$keyword[0]' OR
	tcategory.name LIKE '$keyword[0]' OR
	tincidents.id = '$keyword[0]' OR
	tincidents.user LIKE (SELECT max(id) FROM tusers where firstname LIKE '%$keyword[0]%' or lastname LIKE '%$keyword[0]%')
	)
	AND disable='0'
"; 
	
}	
?>