import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BuildingAssessmentComponent } from './building-assessment.component';

describe('BuildingAssessmentComponent', () => {
  let component: BuildingAssessmentComponent;
  let fixture: ComponentFixture<BuildingAssessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BuildingAssessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BuildingAssessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
