import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LandTaxComponent } from './land-tax.component';

describe('LandTaxComponent', () => {
  let component: LandTaxComponent;
  let fixture: ComponentFixture<LandTaxComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LandTaxComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LandTaxComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
