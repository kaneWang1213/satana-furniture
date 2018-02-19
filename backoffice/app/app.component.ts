import { Component, OnInit, Input, Output } from '@angular/core';

@Component({
  moduleId: module.id,
  selector: 'my-app',
  //template:  '<h1>My Angular 2 App</h1><div>myVariable: {{ myVariable }}</div>'
  templateUrl: 'app.component.html'
  
  
})
export class AppComponent implements OnInit {

    myVariable = "test1223344556676";
    
    constructor() { }

    ngOnInit() {
      
    }

}