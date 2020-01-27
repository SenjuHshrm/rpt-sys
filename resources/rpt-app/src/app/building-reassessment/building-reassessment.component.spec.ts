import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BuildingReassessmentComponent } from './building-reassessment.component';

describe('BuildingReassessmentComponent', () => {
  let component: BuildingReassessmentComponent;
  let fixture: ComponentFixture<BuildingReassessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BuildingReassessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BuildingReassessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
