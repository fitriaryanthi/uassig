@extends('layout.master')

@section('title','Dashboard')
@section('content')
<div class="row mt-2">
  <div class="col-12">
    <h5>Cari tanggal</h5>
    <form action="/search" method="POST">
      @csrf
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
        <input id="tanggalSearch" type="date" @if(isset($tanggal)) value="{{$tanggal}}" @endif name="tanggal"
          class="form-control" required>
        <span class="input-group-btn">
          <button type="submit" class="btn btn-success btn-block">Search</button>
        </span>
      </div>
    </form>
  </div>
</div>
<hr>
<div class="row mt-2">
  <div class="col-12">
    <h3>Tanggal {{$tanggalNow}}</h3>
  </div>
</div>
<!-- Small boxes (Stat box) -->
<div class="row mt-1">

  <div class="container mt-4">
  <div class="row mt-4 mb-4">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
              <center><b>Positif</b></center>
            </div>
            <div class="card-body">
              <h5 class="card-title"><center>Jumlah : {{$jumlahPositif[0]->total}} Orang</center></h5>
            </div>
          </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
              <center><b>Meninggal</b></center>
            </div>
            <div class="card-body">
              <h5 class="card-title"><center>Jumlah : {{$jumlahMeninggal[0]->meninggal}} Orang</center></h5>
            </div>
          </div>    
    </div>
  </div>

  <div class="row mt-4 mb-4">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-success text-white">
              <center><b>Sembuh</b></center>
            </div>
            <div class="card-body">
              <h5 class="card-title"><center>Jumlah : {{$jumlahSembuh[0]->sembuh}} Orang</center></h5>
            </div>
          </div>  
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
              <center><b>Dalam Perawatan</b></center>
            </div>
            <div class="card-body">
              <h5 class="card-title"><center>Jumlah : {{$jumlahDirawat[0]->perawatan}} Orang</center></h5>
            </div>
        </div> 
    </div>
  </div>
    

  <div class="row mt-2">
  <div class="col-12">

    <div class="card card-blue">
      <div class="card-header">
        <h3 class="card-title">Peta Penyebaran Covid Provinsi Bali <strong>{{$tanggalNow}}</strong></h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body no-padding p-0">
        <div class="row">
          <div class="col-12">
            <div class="pad">
              <div id="mapid" style="height: 500px"></div>
            </div>
          </div>
        </div>

      </div>
      <!-- /.card-body -->
      {{-- <div class="card-footer" style="background: white">
        <div class="row">
          <div class="col-6">
            <p>Color Start:</p>
            <input type="color" value="#edff6b" class="form-control" id="colorStart">
          </div>
          <div class="col-6">
            <p>Color End:</p>
            <input type="color" value="#6b6a01" class="form-control" id="colorEnd">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <button class="btn btn-primary form-control" id="btnGenerateColor">Generate Color</button>
          </div>

        </div>
      </div> --}}
    </div>
    <!-- /.card -->
  </div>
</div>


<div class="row mt-2">
  <div class="col-12">

    <div class="card card-maroon">
      <div class="card-header">
        <h3 class="card-title">Covid-19 Provinsi Bali <strong>{{$tanggalNow}}</strong></h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>No</th>
              <th>Kabupaten</th>
              <th>Positif</th>
              <th>Meninggal</th>
              <th>Sembuh</th>
              <th>Dirawat</th>
              {{-- <th>Tanggal</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $item)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>{{ucfirst($item->kabupaten)}}</td>
              <td>{{$item->total}}</td>
              <td>{{$item->meninggal}}</td>
              <td>{{$item->sembuh}}</td>
              <td>{{$item->perawatan}}</td>
              {{-- <td>{{$item->tanggal}}</td> --}}
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>
@endsection
@section("js")
<script src="https://unpkg.com/leaflet-kmz@latest/dist/leaflet-kmz.js"></script>
<script src="https://pendataan.baliprov.go.id/assets/frontend/map/leaflet.markercluster-src.js"></script>
<script src="http://leaflet.github.io/Leaflet.label/leaflet.label.js" charset="utf-8"></script>
<script>
  $(document).ready(function () {
    var dataMaps=null;
    var dataClr=null;
    var clrMap=[
      "edff6b",
      "dcec5d",
      "ccd950",
      "bcc743",
      "acb436",
      "9ba128",
      "8b8f1b",
      "7b7c0e",
      "6b6a01"
    ];
    var tanggal = $('#tanggalSearch').val();
    $.ajax({
      async:false,
      url:'getDataMap',
      type:'get',
      dataType:'json',
      data:{date: tanggal},
      success: function(response){
        dataMaps = response["dataMap"];
        dataClr = response["dataColor"];
      }
    });
    console.log(dataMaps);
    var map = L.map('mapid',{
      fullscreenControl:true,
    });
    
    $('#btnGenerateColor').on('click',function(e){
      var colorStart = $('#colorStart').val();
      var colorEnd = $('#colorEnd').val();
      $.ajax({
        async:false,
        url:'/create-pallete',
        type:'get',
        dataType:'json',
        data:{start: colorStart, end:colorEnd},
        success: function(response){
          clrMap = response;
          setMapAttr();
        }
      });
      
    });
    
    
    map.setView(new L.LatLng(-8.500410, 115.195839),10);
    var OpenTopoMap = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 20,
            // zoomAnimation:true,
            id: 'mapbox/streets-v11',
            // tileSize: 512,
            // zoomOffset: -1,
            accessToken: 'pk.eyJ1Ijoid2lkaWFuYXB3IiwiYSI6ImNrNm95c2pydjFnbWczbHBibGNtMDNoZzMifQ.kHoE5-gMwNgEDCrJQ3fqkQ',
        }).addTo(map);
    OpenTopoMap.addTo(map);
    var defStyle = {opacity:'1',color:'#000000',fillOpacity:'0',fillColor:'#CCCCCC'};
    setMapAttr();
    // var m = L.marker([-8.500410, 115.195839]).bindLabel('A sweet static label!', { noHide: true })
		// 	.addTo(map)
		// 	.showLabel();

    function setMapAttr(){
      var markerIcon = L.icon({
        iconUrl: '/img/marker.png',
        iconSize: [40, 40],
      });
      
      var kmzParser = new L.KMZParser({
          
          onKMZLoaded: function (kmz_layer, name) {
            
              control.addOverlay(kmz_layer, name);
              var markers = L.markerClusterGroup();
              var layers = kmz_layer.getLayers()[0].getLayers();
              console.log(layers[0]);
              layers.forEach(function(layer, index){
                var kbptn  = layer.feature.properties.NAME_2;
                var kcmtn =  layer.feature.properties.NAME_3;
                var klrhn = layer.feature.properties.NAME_4;
                var data;
              
                var STYLE = {opacity:'1',color:'#000',fillOpacity:'1'};
                var HIJAU_MUDA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#81F781'};
                var HIJAU_TUA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#088A08'};
                var KUNING = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#FFFF00'};
                var MERAH_MUDA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#F78181'};
                var MERAH_TUA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#B40404'};
                if(!Array.isArray(dataMaps) || !dataMaps.length == 0){
                // set sub layer default style positif covid
                  // var STYLE = {opacity:'1',color:'#000',fillOpacity:'1',fillColor:'#'+clrMap[index]}; 
                  // layer.setStyle(STYLE);
                    var searchResult = dataMaps.filter(function(it){
                      return it.kecamatan.replace(/\s/g,'').toLowerCase() === kcmtn.replace(/\s/g,'').toLowerCase() &&
                              it.kelurahan.replace(/\s/g,'').toLowerCase() === klrhn.replace(/\s/g,'').toLowerCase();
                    });
                    if(!Array.isArray(searchResult) || !searchResult.length ==0){
                      var item = searchResult[0];
                      if(item.total == 0 ){
                        layer.setStyle(HIJAU_MUDA);  
                      }else if(item.perawatan == 0 && item.total>0 && item.sembuh >= 0 && item.meninggal >=0){
                        layer.setStyle(HIJAU_TUA);
                      }else if(item.ppln ==1 && item.perawatan == 1 && item.total == 1 && item.tl==0 || item.ppdn ==1 && item.perawatan == 1 && item.total == 1 && item.tl==0){
                        layer.setStyle(KUNING);
                      }else if((item.ppln >1 && item.perawatan <= item.ppln && item.sembuh <= item.ppln && item.tl == 0) || (item.ppdn >1 && item.perawatan <= item.ppdn && item.sembuh <= item.ppdn && item.tl == 0)  ){
                        layer.setStyle(MERAH_MUDA);
                      }else{
                        layer.setStyle(MERAH_TUA);
                      }
                      data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';
                    
                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kbptn+'</td>';
                      data +='  </tr>';              
      
                      data +='  <tr >';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kcmtn+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+klrhn+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>PP-LN</td>';
                      data +='    <td>: '+item.ppln+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>PP-DN</td>';
                      data +='    <td>: '+item.ppdn+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>TL</td>';
                      data +='    <td>: '+item.tl+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Lainnya</td>';
                      data +='    <td>: '+item.lainnya+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:green">';
                      data +='    <td>Sembuh</td>';
                      data +='    <td>: '+item.sembuh+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:blue">';
                      data +='    <td>Dalam Perawatan</td>';
                      data +='    <td>: '+item.perawatan+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:red">';
                      data +='    <td>Meninggal</td>';
                      data +='    <td>: '+item.meninggal+'</td>';
                      data +='  </tr>';
                    }else{
                      console.log(klrhn.replace(/\s/g,'').toLowerCase());
                      console.log(kcmtn.replace(/\s/g,'').toLowerCase());
                      data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';
                    
                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kbptn+'</td>';
                      data +='  </tr>';              
      
                      data +='  <tr style="color:red">';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kcmtn+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:red">';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+klrhn+'</td>';
                      data +='  </tr>';
                    }
                    

                    
                    // data +='  <tr style="color:green">';
                    // data +='    <td>Sembuh</td>';
                    // data +='    <td>: '+dataMap[index].sembuh+'</td>';
                    // data +='  </tr>'; 

                    // data +='  <tr style="color:black">';
                    // data +='    <td>Meninggal</td>';
                    // data +='    <td>: '+dataMap[index].meninggal+'</td>';
                    // data +='  </tr>';

                    // data +='  <tr style="color:blue">';
                    // data +='    <td>Dalam Perawatan</td>';
                    // data +='    <td>: '+dataMap[index].dirawat+'</td>';
                    // data +='  </tr>';               
                                  
                    // data +='</table>';
                    // if(kbptn == 'BANGLI'){
                    //   markers.addLayer( 
                    //     L.marker([-8.254251, 115.366936] ,{
                    //       icon: markerIcon
                    //     }).bindPopup(data).addTo(map)
                    //   );
                    // }
                    // else if(kbptn == 'GIANYAR'){
                    //   markers.addLayer( 
                    //     L.marker([-8.422739, 115.255700] ,{
                    //       icon: markerIcon
                    //     }).bindPopup(data).addTo(map)
                    //   );

                    // }else if(kbptn == 'KLUNGKUNG'){
                    //   markers.addLayer( 
                    //     L.marker([-8.487338, 115.380029] ,{
                    //       icon: markerIcon
                    //     }).bindPopup(data).addTo(map)
                    //   );

                    
                      
                    
                }else{
                  // var data = "Tidak ada Data pada tanggal tersebut"
                  layer.setStyle(defStyle);
                  data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';
                    
                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kbptn+'</td>';
                      data +='  </tr>';              
      
                      data +='  <tr>';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kcmtn+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+klrhn+'</td>';
                      data +='  </tr>';  
                }
                layer.bindPopup(data);
                // markers.addLayer(L.marker(getRandomLatLng(map)));
                markers.addLayer( 
                  L.marker(layer.getBounds().getCenter(),{
                    icon: markerIcon
                  }).bindPopup(data)
                );
              });
              map.addLayer(markers);
              kmz_layer.addTo(map);
          }
      });
      kmzParser.load('bali-kelurahan.kmz');
      var control = L.control.layers(null, null, {
          collapsed: true
      }).addTo(map);
      $('.leaflet-control-layers').hide();

    }
  });
</script>
@endsection