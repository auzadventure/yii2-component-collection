<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Paperless File Management</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/dropbox.js/10.31.0/Dropbox-sdk.js" integrity="sha512-N9HOCHrPtjaIcvM52n2i1vbf0us9Q4lSbKxYSGJhehsZuZaQnN4LdiuchQzQBoHloz7/LZJP9zrWZb0Rw9bWUA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
	<div id='app' class='container-lg pt-2'>
		<div class="row">
			<div class="col-8">
				<h2> Files</h2>
			</div>
			<div class="col-4 text-right pt-2">
				<a href='http://paperlesscloud.wesvault.com/pc/acctpro/index.php'>
					Back
				</a>
			</div>
		</div>
		

		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb" >
		    <li class="breadcrumb-item" v-for='(folder,i) in pathA'>
		    	<a href='#' @click.prevent='backFolder(i)'>
		    		{{folder}}
		    	</a>
		    </li>

		  </ol>
		</nav>


		<div class="row">
			<div class="col-md-6">
				<div class="card border-primary">
				<div class="card-header">
					<div class="row">
						<div class="col-6">
							Files <b>{{ files.length }}</b>
						</div>
						<div class="col-6 text-right">

							<div v-show='wait' class="spinner-border spinner-border-sm text-secondary" role="status">
						  		<span class="sr-only">Loading...</span>
							</div>

							<a href="#" class='btn btn-primary btn-sm' @click='showUpload =!showUpload'>
								<i class="bi bi-upload"></i>
							</a>


							<a href='#' class='btn btn-primary btn-sm' @click='showCreateFolder=!showCreateFolder'>
								<i class="bi bi-folder-plus"></i>
							</a>

							<a href='#' class='btn btn-secondary btn-sm' @click='listFiles(folder)'>
								<i class="bi bi-arrow-counterclockwise"></i>
							</a>
						</div>
					</div>
				</div>

				<div class="card-body">
			
					<table class='table'>
						<thead class='thead-light'>
					    <tr>
					      
					      <th scope="col">Filename</th>
					      <th scope="col" class='small'>View</th>
					      <th scope="col" class='small'>Delete</th>
					    </tr>
						</thead>
						<tbody>
							<tr v-for='file in files'>
								
								<td>
								<span v-if='file[".tag"] == "folder"'>
									<a href='#' @click.prevent='changeFolder(file.path_lower)'>
									<i class="bi bi-folder mr-2"></i>
									{{file.path_lower | shortenPath}}

								</a>
								</span>
								<span v-else>
									<i class="bi bi-file-earmark"></i> {{file.name}} 
								</span>
								</td>
								<td>
									<a href='#' v-if='file[".tag"] != "folder"' 
									   @click.prevent='goLink(file.path_lower)'
									>
									  <i class='bi bi-link'></i>
									</a>
									
								<td>
									<a href='#' @click.prevent='deleteFile(file.path_lower)'> <i class='bi bi-x'></i>
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>				
		</div>
			<div class="col-md-6">

			<div v-show='sharedLink != ""' class="card">
				<div class="card-header">
					View Link
				</div>
				<div class="card-body">
					<input class='form-control form-control-sm"' v-model='sharedLink' />
					<p class='mt-2'>
						<a :href='sharedLink' class='btn btn-primary' target='_blank'>
						Go To Url
						</a>
					
						<a href='#' @click='sharedLink=""' class='btn btn-secondary'>Clear</a>
					</p>
					
				</div>
			</div>


			<div v-show='showUpload' class="card">
				<div class="card-header">
					Upload A file to this directoy
				</div>
				<div  class="card-body">
					<form>
					<div class="form-group">
						<input type="file" id="file" 
						class="form-control-file p-2"
						ref="file" @change="handleFileUpload"/>
					</div>
					<div class="row">
						<div class="form-group col-8">
						<input class='form-control form-control-sm' v-model='filename' placeholder="New File Name (no spaces)" @keydown.space.prevent>
						</div>
						<div class="col-4">
							<button @click.prevent="submitFile" class='btn btn-primary'>
								Upload
							</button>
							<span v-html='results'></span> 
						</div>
					</div>
					
					
					
				</div>				
			</div>

			<div v-show='showCreateFolder' class="card mt-2">
				<div class="card-header">
					Create New Folder
				</div>
				<div class="card-body row">
					<form>
					<div class="form-group col-md-8">
						<input class='form-control form-control-sm"' v-model='foldername' placeholder="New Folder Name" pattern="[^' ']+" />
					</div>
					<div class="col-md-4">
						<button v-on:click="createFolder" class='btn btn-primary'>
						Create
						</button> 
					</div>
					<span v-html='results' class='text-warning'></span>
				</div>				
			</div>

			</div>
		</div>
	</div>

<script>
var  app = new Vue({
	el: '#app',
	data: {
		file:'',
		filename: '',
		wait: false,
		results:'',
		files:[],
		dbx:'',
		sharedLink:'',
		
		// Set the Top Level Folder
		 
		folder: '/',  
		
		showCreateFolder: false,
		
		showUpload: false,
		
		foldername: '',
		
		path : ''
	},

	mounted() {
		this.dbx = this.dbx_connect();
		this.folder = this.folder + this.path;
		this.listFiles(this.folder);

	},	

	methods: {
		
		handleFileUpload(e) {
			this.file = e.target.files[0]
			var filename = this.file.name.replaceAll(' ', '_');
			filename = filename.toLowerCase();
			this.filename = filename.replace(/\.[^/.]+$/, ""); 
			this.results = 'Ready';
		},
		
		dbx_connect() {
			const ACCESS_TOKEN = 'XXXXXXXXXXXXXXXXXXXXXXX';

			var dbx = new Dropbox.Dropbox({ accessToken: ACCESS_TOKEN });		
			return dbx;
		},
		
		submitFile() {
			let dbx = this.dbx
			var file = this.file; 
			var self = this 
			var ext = file.name.split('.').pop();
			if(this.filename =='') {
				this.results = '<span class="text-warning">Please Input Filename</span>';
				return; 
			}
			var filename = this.filename + '.' + ext;
			filename = filename.toLowerCase();

			dbx.filesUpload({path: this.folder + '/' + filename, contents: file})
			  .then(function(response) {
				self.results = '<span class="text-success">File Upload Complete!</span>';
				//self.getLink(filename,'');
				self.listFiles(self.folder);
				self.file = '';
				self.filename = '';
			  })
			  .catch(function(error) {
				console.error(error);
			  });	
		},

		deleteFile(path) {
			let self = this;

			if(!confirm("Do You want to delete this file or Folder?")) return ''; 

			console.log(path)
			this.dbx.filesDeleteV2({'path': path})
				.then(function(response) {
					self.listFiles(self.folder);
				}).catch(function(err){
					console.error(err)
				})
			
		},
		
		listFiles(folder) {
			let self = this
			self.wait = true; 
			this.dbx.filesListFolder({path: folder + '/'})
				.then(function(response) {
					//self.files = response.entries
					self.files = response.result.entries
					console.log(self.files)

					self.files.sort((a,b)=> {
						if(a.is_downloadable && !b.is_downloadable) return 1;
						if(!a.is_downloadable && b.is_downloadable) return -1;
						
						/*
						if(a.name < b.name) return -1;
						if(a.name > b.name) return 1;
						return 0;
						*/
						return a.name.localeCompare(b.name,'en',{'numeric':true});
					});
					self.wait = false;
			
			})		
				
			
		},
		
		goLink(filename) {
			var self = this
			self.wait = true; 
			this.dbx.sharingCreateSharedLink({ 'path': filename})
				.then ( (response) => {
					self.sharedLink = response.result.url
					self.wait = false;
				}) 
		},

		previewFile(filename) {
			var self = this
			self.wait = true; 
			this.dbx.filesGetPreview({ 'path': filename})
				.then ( (response) => {
					console.log(response)
					self.wait = false;
				}) 	
			
		},

		
		copyAction(e) {
			e.preventDefault();
		
			let code = this.sharedLink;
			if(code =='') return ;
			
			const el = document.createElement('textarea');
			el.value = code.trim();
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);				

			$('#video-tips').show();			
		},

		createFolder() {
			var path = this.folder + '/' + this.foldername;
			var self = this; 
			self.wait = true; 
			this.dbx.filesCreateFolderV2({'path': path})
				.then(function(response) {
					self.listFiles(self.folder);
					self.wait = false;
				}).catch(function(err){
					console.error(err)
					self.wait = false;
				})
		},

		//change a folder
		changeFolder(path) {
			this.folder = path;
			this.listFiles(this.folder)
		},

		backFolder(levels) {
			var pathA = this.pathA
			console.log(pathA)
			var a = [];
			console.log(levels);

			for(let i = 0; i < levels+1; i++) {
				a.push(pathA[i]);
			}
			this.folder = '/' + a.join('/');
			this.listFiles(this.folder);
		}
	},

	computed: {
		pathA() {
			return this.folder.substring(1).split('/');
		},
	},

	filters: {
		shortenPath(path) {
			return path.substring(path.lastIndexOf("/") + 1);
		}
	}

})
</script>



</body>
</html>