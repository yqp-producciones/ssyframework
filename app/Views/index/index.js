class IndexComponent extends Component {
    galerias=[];
    constructor() { super('app-index');
        this.Prefijo = 'afk';
        this.form = new FormGroup('formulario');
        this.image_group = new CardGroup();
        this.image_group.Columns = 4;
        this.galeria = new Control('galeria');
        this.get_galeria();
        this.form.form.submit(()=>this.guardar());
    }

    async get_galeria(){
        this.galerias=[];
        this.image_group.Items=[];
        let response = await new HttpClient().Post('galeria/select',{op:'sel-fill'});
        if(response.result){
            this.galerias = response.data;
            for (let item of this.galerias) {
                let card = new Card();
                card.Title = item.nombre;
                card.Descripcion = item.nombre;
                card.Image = item.url;
                card.Buttons.Container.addClass(' w-100');
                card.Elements.title.addClass('d-none');
                card.Elements.description.addClass('d-none');
                card.Elements.img.css('height','200px');
                card.Buttons.Add(new Button('btn btn-primary btn-block',[new I('fa fa-eye'),' Ver Imagen']));
                card.Buttons.Add(new Button('btn btn-danger ml-1',[new I('fa fa-trash')]).data({current:item}).click((e)=>{this.delete_image(e.currentTarget);}));
                this.image_group.Add(card);
            }
            //this.image_group.Show();
            this.galeria.html('').append(this.image_group.Container);
        }
    }

    async guardar(){
        let data = this.form.GetFormData;
        data.append('op','ins-default');
        let response = await new HttpClient().Post('galeria/insert',data,true);
        if(response.result){
            MessageBox.Show(response.message,response.title,MessageBox.Icons.Success);
            this.get_galeria();
            this.form.Clear();
        } else {MessageBox.Show(response.message,response.title,MessageBox.Icons.Error);}
    }
    async delete_image(e){
        let current = $(e).data('current');
        let response = await new HttpClient().Post('galeria/delete',{op:'del-default',id:current.id,url:current.imagen});
        if(response.result){
            this.get_galeria();
            MessageBox.Show(response.message,response.title,MessageBox.Icons.Success);
        } else {
            MessageBox.Show(response.message,response.title,MessageBox.Icons.Error);
        }
    }
}
new IndexComponent();

