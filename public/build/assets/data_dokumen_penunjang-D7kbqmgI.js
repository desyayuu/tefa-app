$(document).ready(function(){let i=[],r=1,l=3;u(1),typeof Swal>"u"&&document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>'),window.location.hash==="#dokumen-penunjang-section"&&setTimeout(function(){p()},300),$(document).on("click",".pagination-link",function(e){e.preventDefault(),r=$(this).data("page"),u(r),$("html, body").animate({scrollTop:$("#tableDokumenPenunjang").offset().top-100},500)});function p(){const e=$("#dokumen-penunjang-section");e.length&&$("html, body").animate({scrollTop:e.offset().top-80},500)}function c(e,n,a){const t=a>0?(e-1)*n+1:0,o=Math.min(e*n,a);$("#dokumenPaginationInfo").html(`Showing ${t} to ${o} of ${a} entries`)}$("#searchDokumenForm").on("submit",function(e){e.preventDefault();const n=$("#searchDokumenPenunjang").val(),a=new URL(window.location.href);a.searchParams.set("searchDokumenPenunjang",n),a.hash="dokumen-penunjang-section",window.history.pushState({},"",a.toString()),r=1,u(r),p()}),$("#btnTambahDokumen").on("click",function(){const e=$("#nama_dokumen_penunjang").val(),n=$("#jenis_dokumen_penunjang_id").val(),a=$("#jenis_dokumen_penunjang_id option:selected").text(),t=$("#file_dokumen_penunjang")[0];if(!e||!n||t.files.length===0){typeof Swal<"u"?Swal.fire({icon:"error",title:"Formulir Tidak Lengkap",text:"Silakan lengkapi semua field formulir"}):alert("Silakan lengkapi semua field formulir");return}const o={id:Date.now(),nama_dokumen_penunjang:e,jenis_dokumen_penunjang_id:n,jenis_dokumen:a,file:t.files[0],fileName:t.files[0].name};i.push(o),h(),$("#nama_dokumen_penunjang").val(""),$("#jenis_dokumen_penunjang_id").val(""),$("#file_dokumen_penunjang").val(""),i.length===1&&($("#previewDokumenSection").removeClass("d-none"),$("html, body").animate({scrollTop:$("#previewDokumenSection").offset().top-100},500))});function h(){const e=$("#previewDokumenTable tbody");e.empty(),i.forEach((n,a)=>{e.append(`
                <tr>
                    <td>${a+1}</td>
                    <td>${n.nama_dokumen_penunjang}</td>
                    <td>${n.jenis_dokumen}</td>
                    <td>
                        <span class="text-truncate-file">${n.fileName}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-preview-delete" data-id="${n.id}">
                            <svg width="20" height="20" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.7379 3.12109C17.9743 3.12126 22.2193 7.314 22.2193 12.4854C22.2191 17.6565 17.9742 21.8485 12.7379 21.8486C7.50141 21.8486 3.25566 17.6566 3.25546 12.4854C3.25546 7.3139 7.50129 3.12109 12.7379 3.12109ZM9.15878 7.55273C8.76368 7.23427 8.1806 7.25655 7.8121 7.62012C7.44362 7.98402 7.42019 8.56086 7.74277 8.95117L7.8121 9.02539L11.3141 12.4844L7.8121 15.9443C7.41924 16.3324 7.41918 16.9616 7.8121 17.3496C8.20503 17.7374 8.84205 17.7375 9.23495 17.3496L12.7369 13.8896L16.2398 17.3496C16.6327 17.7376 17.2697 17.7373 17.6627 17.3496C18.0556 16.9615 18.0556 16.3324 17.6627 15.9443L14.1598 12.4844L17.6637 9.02539L17.732 8.95117C18.0546 8.56086 18.0321 7.98402 17.6637 7.62012C17.2952 7.25624 16.7112 7.23418 16.316 7.55273L16.2408 7.62012L12.7369 11.0791L9.23495 7.62012L9.15878 7.55273Z" fill="#E56F8C"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `)}),$(".btn-preview-delete").on("click",function(){const n=$(this).data("id");f(n)})}function f(e){i=i.filter(n=>n.id!==e),i.length===0?$("#previewDokumenSection").addClass("d-none"):h()}$("#btnBatalPreview").on("click",function(){i=[],$("#previewDokumenSection").addClass("d-none")}),$("#btnSimpanDokumen").on("click",function(){if(i.length===0){Swal.fire({icon:"error",title:"Tidak Ada Dokumen",text:"Tidak ada dokumen untuk disimpan"});return}let e=0,n=0;const a=i.length;Swal.fire({title:"Menyimpan Dokumen...",html:`Menyimpan 0 dari ${a}`,allowOutsideClick:!1,didOpen:()=>{Swal.showLoading()}});const t=o=>{if(o>=i.length){n===0?Swal.fire({icon:"success",title:"Berhasil",text:`${e} dokumen berhasil disimpan`}):Swal.fire({icon:"warning",title:"Selesai Dengan Peringatan",html:`${e} dokumen berhasil disimpan<br>${n} dokumen gagal disimpan`}),i=[],$("#previewDokumenSection").addClass("d-none"),setTimeout(function(){r=1,u(r)},500);return}const m=i[o],s=new FormData;s.append("proyek_id",$('input[name="proyek_id"]').val()),s.append("nama_dokumen_penunjang",m.nama_dokumen_penunjang),s.append("jenis_dokumen_penunjang_id",m.jenis_dokumen_penunjang_id),s.append("file_dokumen_penunjang",m.file),s.append("_token",$('meta[name="csrf-token"]').attr("content")),Swal.update({html:`Menyimpan ${o+1} dari ${a}`}),$.ajax({url:"/mahasiswa/proyek/dokumen-penunjang",type:"POST",data:s,processData:!1,contentType:!1,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(d){d.success?e++:(n++,console.error("Error saving document:",d.message)),t(o+1)},error:function(d){n++,console.error("Error saving document:",d.responseText),t(o+1)}})};t(0)});function u(e=1){const n=$('input[name="proyek_id"]').val(),a=$("#searchDokumenPenunjang").val()||"";$("#tableDokumenPenunjang tbody").html(`
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2 text-muted small">Memuat data...</p>
                </td>
            </tr>
        `),$("#dokumenPagination").html(""),$.ajax({url:`/mahasiswa/proyek/${n}/dokumen-penunjang`,type:"GET",data:{search:a,page:e,per_page:l},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){if(t&&t.success&&t.data)if(t.data.total!==void 0?c(t.data.current_page||e,t.data.per_page||l,t.data.total||0):t.pagination&&c(t.pagination.current_page||e,t.pagination.per_page||l,t.pagination.total||0),t.data.data){const o=t.data.data;o&&o.length>0?(g(o),t.pagination&&t.pagination.html?$("#dokumenPagination").html(t.pagination.html):$("#dokumenPagination").html("")):(k(),$("#dokumenPagination").html(""))}else Array.isArray(t.data)&&t.data.length>0?g(t.data):k();else k(),$("#dokumenPagination").html(""),c(1,l,0)},error:function(t){console.error("Error loading dokumen:",t.responseText),$("#tableDokumenPenunjang tbody").html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 9L9 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 9L15 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Terjadi kesalahan saat memuat data</p>
                            <p class="small text-muted">Silakan coba lagi nanti</p>
                        </td>
                    </tr>
                `),$("#dokumenPagination").html(""),c(1,l,0)}})}function g(e){const n=$("#tableDokumenPenunjang tbody");if(n.empty(),!e||!Array.isArray(e)||e.length===0){k();return}$("#emptyDokumenMessage").addClass("d-none"),e.forEach((a,t)=>{if(!a||typeof a!="object"){console.error("Invalid dokumen data:",a);return}const o=a.dokumen_penunjang_proyek_id,m=a.nama_dokumen_penunjang||"Tidak ada nama",s=a.jenis_dokumen||"Tidak diketahui",d=a.created_at;n.append(`
                <tr>
                    <td>${m}</td>
                    <td>${s}</td>
                    <td>${w(d)}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="/mahasiswa/proyek/dokumen-penunjang/download/${o}" 
                            class="btn btn-action-download" 
                            title="Download">
                                <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="6.2425" cy="6.32" rx="6.2425" ry="6.32" transform="matrix(4.42541e-08 -1 -1 -4.31754e-08 18.96 20.8086)" fill="#E4F8EB"/>
                                    <path d="M10.0067 13.0054L12.64 15.6064M12.64 15.6064L15.2733 13.0054M12.64 15.6064L12.64 5.20228" stroke="#00BC39" stroke-linecap="round"/>
                                </svg>
                            </a>
                            <button type="button" class="btn btn-action-delete btn-delete-dokumen" title="Hapus" data-id="${o}">
                                <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5576 3.12109C17.7479 3.12109 21.9551 7.3139 21.9551 12.4854C21.9549 17.6566 17.7478 21.8486 12.5576 21.8486C7.36753 21.8486 3.16035 17.6566 3.16016 12.4854C3.16016 7.31393 7.36741 3.12115 12.5576 3.12109ZM9.01367 7.54883C8.62018 7.22898 8.03966 7.25268 7.67285 7.61816C7.30632 7.98355 7.28285 8.56113 7.60352 8.95312L7.67285 9.0293L11.1406 12.4844L7.67285 15.9404C7.28162 16.3302 7.28162 16.9627 7.67285 17.3525C8.06401 17.7421 8.69767 17.742 9.08887 17.3525L12.5576 13.8955L16.0264 17.3525C16.4176 17.7422 17.0522 17.7423 17.4434 17.3525C17.8344 16.9627 17.8345 16.3302 17.4434 15.9404L13.9736 12.4844L17.4424 9.0293L17.5117 8.95312C17.8325 8.56109 17.8091 7.98357 17.4424 7.61816C17.0756 7.25268 16.495 7.22898 16.1016 7.54883L16.0254 7.61816L12.5576 11.0723L9.08984 7.61816L9.01367 7.54883Z" fill="#E56F8C"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `)}),v()}function w(e){if(!e)return"-";const n=new Date(e);return isNaN(n.getTime())?e:n.toLocaleDateString("id-ID",{day:"2-digit",month:"long",year:"numeric"})}function k(){$("#tableDokumenPenunjang tbody").empty(),$("#emptyDokumenMessage").removeClass("d-none");const e=$("#searchDokumenPenunjang").val()||"";e?$("#emptyDokumenMessage div").html(`
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <circle cx="11" cy="11" r="8" stroke="#8E8E8E" stroke-width="1.5"/>
                    <path d="M16.5 16.5L21 21" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 7V11" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 15H11.01" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-muted mb-1">Tidak ada dokumen ditemukan dengan kata kunci: <strong>"${e}"</strong></p>
            `):$("#emptyDokumenMessage div").html(`
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <path d="M8 2V5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M16 2V5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M3.5 9.08984H20.5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M19.21 15.7698L15.67 19.3098C15.53 19.4498 15.4 19.7098 15.37 19.8998L15.18 21.2498C15.11 21.7398 15.45 22.0798 15.94 22.0098L17.29 21.8198C17.48 21.7898 17.75 21.6598 17.88 21.5198L21.42 17.9798C22.03 17.3698 22.32 16.6598 21.42 15.7598C20.53 14.8698 19.82 15.1598 19.21 15.7698Z" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.7002 16.2798C19.0002 17.3598 19.8402 18.1998 20.9202 18.4998" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5V12" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.9955 13.7002H12.0045" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.29431 13.7002H8.30329" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.29431 16.7002H8.30329" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="text-muted">Belum ada dokumen penunjang</p>
            `)}function v(){$(".btn-delete-dokumen").on("click",function(){const e=$(this).data("id");Swal.fire({title:"Konfirmasi Hapus",text:"Apakah Anda yakin ingin menghapus dokumen ini?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",confirmButtonText:"Hapus",cancelButtonText:"Batal"}).then(n=>{n.isConfirmed&&j(e)})})}function j(e){$.ajax({url:`/mahasiswa/proyek/dokumen-penunjang/${e}`,type:"DELETE",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(n){n.success?(Swal.fire({icon:"success",title:"Berhasil",text:"Dokumen penunjang berhasil dihapus"}),u(r)):Swal.fire({icon:"error",title:"Gagal",text:n.message||"Gagal menghapus dokumen penunjang"})},error:function(n){let a="Terjadi kesalahan pada server";n.responseJSON&&n.responseJSON.message&&(a=n.responseJSON.message),Swal.fire({icon:"error",title:"Gagal",text:a})}})}});
