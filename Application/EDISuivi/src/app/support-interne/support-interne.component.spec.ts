import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SupportInterneComponent } from './support-interne.component';

describe('SupportInterneComponent', () => {
  let component: SupportInterneComponent;
  let fixture: ComponentFixture<SupportInterneComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SupportInterneComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SupportInterneComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
