<?php
require_once 'vendor/autoload.php';
require_once "random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=asrulst;AccountKey=DdzR5e4pY8b+dsI/whj64GjjQJ5qN9M3fymvbIvrdi942hCij1x5s5N36WCVuvY8zHk/avYRDuaMhdm1CBvshw==;EndpointSuffix=core.windows.net";

$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "asrulblockblobs";
	
if (isset($_POST['submit'])) {
	$fileToUpload = $_FILES["fileToUpload"]["name"];
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	echo fread($content, filesize($fileToUpload));
		
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}	
	
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Submission 2</title>
		<link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">
		<!-- Bootstrap core CSS -->
		<link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="starter-template.css" rel="stylesheet">
	</head>
	
	<body>
			<main role="main" class="container">
				<div class="starter-template"> <br><br><br>
					<h1>Upload Foto</h1>
					<br>
					<p class="lead">Pilih foto anda<br> Kemudian klik <b>Upload</b>. Untuk melihat detail foto, pilih <b>Detail Gambar</b> pada tabel.</p>
					<span class="border-top my-3"></span>
				</div>				
				<div class="mt-4 mb-2">
					<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
						<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required>
						<input type="submit" name="submit" value="Upload">
					</form>
				</div>
				<br>
				
				<table class='table table-hover'>
					<thead>
						<tr>
							<th>File Name</th>
							<th>File URL</th>
							<th>Action</th>
						</tr>
					</thead>
					
					<tbody>
						<?php
						do {
							foreach ($result->getBlobs() as $blob) {
						?>						
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="detail.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Detail Gambar" class="btn btn-primary">
								</form>
							</td>
						</tr>
						<?php
							} $listBlobsOptions->setContinuationToken($result->getContinuationToken());
						} while($result->getContinuationToken());
						?>
					</tbody>
				</table>
				</div>				
	</body>
</html>
