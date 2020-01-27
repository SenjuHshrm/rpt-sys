import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RPTOPComponent } from './rptop.component';

describe('RPTOPComponent', () => {
  let component: RPTOPComponent;
  let fixture: ComponentFixture<RPTOPComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RPTOPComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RPTOPComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
