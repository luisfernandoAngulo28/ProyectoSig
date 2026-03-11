<div id="filtro-city" style="display:none">
    <div class="row d-flex" style=" align-items:center; justiy-content:end">
        <div class="col-sm-2">
            <div>
                <label>Filtro por Ciudad</label>
                <select name="f_city_id[]" id="city-filter" aria-controls="general-list"
                    class="form-control input-sm">
                </select>
            </div>
        </div>
        <div class="col-sm-2" style="display:none;" id="div-roles">
            <div>
                <label>Filtro por Roles </label>
                <select name="f_role_user[]" id="roles-filter" aria-controls="general-list"
                    class="form-control input-sm">
                </select>
            </div>
        </div>
        <div class="col-sm-1">
            <div>
                <button type="submit" name="button" class="btn btn-outline-primary mt-2" 
                    style="border-radius: 5px !important; color:#016d21">Buscar</button>
            </div>
        </div>
    </div>
</div>


<script>
    async function init() {
        try {
            const response = await getItems('/customer-admin/get-cities');
            document.getElementById('city-filter').innerHTML = response;
        } catch (error) {
            document.getElementById('city-filter').innerHTML = response;
        }

        // FILTREO DE ROLES 
        const filtroRoles = document.getElementById('field_f_role_user');
        if (filtroRoles !== null) {
            document.getElementById('div-roles').style.display = 'block';
            try {
                const response = await getItems('/customer-admin/get-roles');
                document.getElementById('roles-filter').innerHTML = response;
            } catch (error) {
                document.getElementById('roles-filter').innerHTML = response;
            }
        }
    }

    init()

    async function getItems(url, message = '') {
        let html = `<option value="" disabled selected>Seleccionar filtro ${message}</option>`
        try {
            const response = await fetch(url)
            const data = await response.json();
            Object.values(data.data).forEach(element => {
                html += `<option value="${element.id}">${element.name}</option>`
            });
            // document.getElementById('city-filter').innerHTML = html;
            return html;
        } catch (error) {
            console.log(error)
            return 'error';
        }
    }
</script>
